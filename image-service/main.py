from fastapi import FastAPI, File, UploadFile, HTTPException, Depends, status, Form
from fastapi.responses import FileResponse, JSONResponse, HTMLResponse
from fastapi.middleware.cors import CORSMiddleware
from fastapi.security import OAuth2PasswordBearer, OAuth2PasswordRequestForm
from fastapi.staticfiles import StaticFiles
import os
import shutil
import uuid
import json
from datetime import datetime, timedelta
import time
from typing import List, Optional
import uvicorn
from pydantic import BaseModel
import aiofiles
import imghdr
import logging

app = FastAPI(title="Image Storage Service", 
             description="API để lưu trữ và quản lý hình ảnh phim",
             version="1.0.0")

# Thêm CORS để frontend có thể gọi API
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  # Trong môi trường production, hãy giới hạn nguồn gốc
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Cấu hình logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

# Thư mục lưu trữ ảnh
UPLOAD_DIR = "storage"
METADATA_FILE = os.path.join(UPLOAD_DIR, "metadata.json")

# Tạo thư mục public nếu chưa tồn tại
PUBLIC_DIR = "public"
if not os.path.exists(PUBLIC_DIR):
    os.makedirs(PUBLIC_DIR)

# Mount thư mục static để phục vụ các file tĩnh
app.mount("/static", StaticFiles(directory=PUBLIC_DIR), name="static")

# Giới hạn kích thước file (10MB)
MAX_FILE_SIZE = 10 * 1024 * 1024  # 10MB

# Các định dạng ảnh được chấp nhận
ALLOWED_IMAGE_TYPES = ["jpeg", "png", "gif", "bmp", "webp"]

# Đảm bảo thư mục lưu trữ tồn tại
if not os.path.exists(UPLOAD_DIR):
    os.makedirs(UPLOAD_DIR)

# Tạo file metadata nếu chưa tồn tại
if not os.path.exists(METADATA_FILE):
    with open(METADATA_FILE, "w") as f:
        json.dump([], f)

# Đọc metadata
def read_metadata():
    try:
        with open(METADATA_FILE, "r") as f:
            return json.load(f)
    except (json.JSONDecodeError, FileNotFoundError):
        return []

# Lưu metadata
def save_metadata(metadata):
    with open(METADATA_FILE, "w") as f:
        json.dump(metadata, f, indent=2)

# Class cho metadata của ảnh
class ImageMetadata(BaseModel):
    id: str
    filename: str
    original_filename: str
    content_type: str
    size: int
    upload_time: str
    path: str

# Kiểm tra xem file có phải là ảnh hợp lệ không
def validate_image(file_path):
    img_type = imghdr.what(file_path)
    if img_type not in ALLOWED_IMAGE_TYPES:
        os.remove(file_path)
        return False
    return True

# API chính
@app.get("/", response_class=HTMLResponse)
async def read_root():
    try:
        with open(os.path.join(PUBLIC_DIR, "index.html"), "r") as file:
            html_content = file.read()
        return HTMLResponse(content=html_content)
    except Exception as e:
        logger.error(f"Error serving main page: {str(e)}")
        return HTMLResponse(content="<html><body><h1>Image Upload Service</h1><p>Error loading page. Please try again later.</p></body></html>")

@app.post("/upload")
async def upload_file(file: UploadFile = File(...)):
    try:
        # Kiểm tra kích thước file
        file.file.seek(0, 2)  # Di chuyển con trỏ đến cuối file để lấy kích thước
        file_size = file.file.tell()  # Lấy vị trí hiện tại của con trỏ (chính là kích thước file)
        file.file.seek(0)  # Đặt lại con trỏ về đầu file
        
        if file_size > MAX_FILE_SIZE:
            raise HTTPException(
                status_code=status.HTTP_413_REQUEST_ENTITY_TOO_LARGE,
                detail=f"File size exceeds maximum allowed size of {MAX_FILE_SIZE / (1024 * 1024)}MB"
            )
        
        # Kiểm tra phần mở rộng của file
        file_ext = os.path.splitext(file.filename)[1].lower()
        if file_ext not in [".jpg", ".jpeg", ".png", ".gif", ".bmp", ".webp"]:
            raise HTTPException(
                status_code=status.HTTP_415_UNSUPPORTED_MEDIA_TYPE,
                detail="File type not supported. Please upload an image file."
            )
        
        # Tạo tên file duy nhất để tránh ghi đè
        unique_id = str(uuid.uuid4())
        # COỐ Ý: Không xử lý tên file an toàn, giữ nguyên tên file từ người dùng để có thể chèn path traversal
        safe_filename = file.filename
        
        # COỐ Ý: Cho phép Path Traversal trong đường dẫn file
        # Không kiểm tra và lọc đường dẫn, cho phép người dùng chỉ định thư mục lưu trữ
        file_path = os.path.join(UPLOAD_DIR, safe_filename)
        
        # COỐ Ý: Không xác thực đường dẫn cuối cùng có nằm trong UPLOAD_DIR hay không
        # Điều này cho phép người dùng chèn "../" để ghi file ra ngoài thư mục lưu trữ
        
        # Lưu file 
        with open(file_path, "wb") as buffer:
            # Đơn giản hóa quá trình ghi file để dễ khai thác lỗ hổng
            shutil.copyfileobj(file.file, buffer)
        
        # Kiểm tra xem file có phải là ảnh hợp lệ không
        if not validate_image(file_path):
            raise HTTPException(
                status_code=status.HTTP_415_UNSUPPORTED_MEDIA_TYPE,
                detail="Invalid image file."
            )
        
        # Tạo metadata cho ảnh
        now = datetime.now().isoformat()
        metadata = {
            "id": unique_id,
            "filename": safe_filename,
            "original_filename": file.filename,
            "content_type": file.content_type,
            "size": file_size,
            "upload_time": now,
            "path": file_path
        }
        
        # Lưu metadata
        all_metadata = read_metadata()
        all_metadata.append(metadata)
        save_metadata(all_metadata)
        
        logger.info(f"Successfully uploaded file: {file.filename}")
        
        # Trả về thông tin về file đã upload
        return {"id": unique_id, "filename": safe_filename}
        
    except Exception as e:
        logger.error(f"Error uploading file: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))

# COỐ Ý: Thêm endpoint dễ bị tấn công path traversal
@app.get("/poster/{filename}")
async def get_poster(filename: str):
    try:
        # COỐ Ý: Không xác thực đường dẫn, cho phép path traversal
        # Người dùng có thể sử dụng "../" để truy cập các file khác trên hệ thống
        file_path = os.path.join(UPLOAD_DIR, filename)
        
        logger.info(f"Accessing file: {file_path}")
        
        # COỐ Ý: Không kiểm tra xem file có nằm trong UPLOAD_DIR không
        if os.path.exists(file_path):
            return FileResponse(file_path)
        
        raise HTTPException(status_code=404, detail="Image not found")
    except Exception as e:
        logger.error(f"Error retrieving poster: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))

# COỐ Ý: Thêm endpoint dễ bị tấn công path traversal với parameter khác
@app.get("/image-by-path")
async def get_image_by_path(path: str):
    try:
        # COỐ Ý: Không xác thực đường dẫn, cho phép truy cập bất kỳ file nào
        file_path = path
        
        logger.info(f"Accessing file with direct path: {file_path}")
        
        if os.path.exists(file_path):
            return FileResponse(file_path)
        
        raise HTTPException(status_code=404, detail="File not found")
    except Exception as e:
        logger.error(f"Error accessing file: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))

@app.get("/image/{image_id}")
async def get_image(image_id: str):
    try:
        # Lấy thông tin ảnh từ metadata
        metadata = read_metadata()
        image_info = next((item for item in metadata if item["id"] == image_id), None)
        
        if not image_info:
            raise HTTPException(status_code=404, detail="Image not found")
        
        # COỐ Ý: Không xác thực đường dẫn đầy đủ
        file_path = os.path.join(UPLOAD_DIR, image_info["filename"])
        
        if not os.path.exists(file_path):
            # Nếu file không tồn tại, loại bỏ metadata tương ứng
            metadata = [item for item in metadata if item["id"] != image_id]
            save_metadata(metadata)
            raise HTTPException(status_code=404, detail="Image file not found")
            
        return FileResponse(
            file_path, 
            media_type=image_info["content_type"],
            filename=image_info["original_filename"]
        )
        
    except HTTPException:
        raise
    except Exception as e:
        logger.error(f"Error retrieving image: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))

@app.get("/images")
async def list_images():
    try:
        metadata = read_metadata()
        # Chỉ trả về thông tin cần thiết, không bao gồm đường dẫn đầy đủ
        return {
            "files": [
                {
                    "id": item["id"],
                    "filename": item["filename"],
                    "original_filename": item["original_filename"],
                    "content_type": item["content_type"],
                    "size": item["size"],
                    "upload_time": item["upload_time"]
                } for item in metadata
            ]
        }
    except Exception as e:
        logger.error(f"Error listing images: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))

@app.delete("/image/{image_id}")
async def delete_image(image_id: str):
    try:
        metadata = read_metadata()
        image_info = next((item for item in metadata if item["id"] == image_id), None)
        
        if not image_info:
            raise HTTPException(status_code=404, detail="Image not found")
        
        file_path = os.path.join(UPLOAD_DIR, image_info["filename"])
        
        # Xóa file nếu tồn tại
        if os.path.exists(file_path):
            os.remove(file_path)
        
        # Cập nhật metadata
        metadata = [item for item in metadata if item["id"] != image_id]
        save_metadata(metadata)
        
        return {"message": "Image deleted successfully"}
        
    except HTTPException:
        raise
    except Exception as e:
        logger.error(f"Error deleting image: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))

# API để lấy thông tin chi tiết về một ảnh
@app.get("/image/{image_id}/info")
async def get_image_info(image_id: str):
    try:
        metadata = read_metadata()
        image_info = next((item for item in metadata if item["id"] == image_id), None)
        
        if not image_info:
            raise HTTPException(status_code=404, detail="Image not found")
            
        # Không trả về đường dẫn đầy đủ
        safe_info = {k: v for k, v in image_info.items() if k != "path"}
        return safe_info
        
    except HTTPException:
        raise
    except Exception as e:
        logger.error(f"Error retrieving image info: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))

if __name__ == "__main__":
    uvicorn.run(app, host="0.0.0.0", port=8000) 