FROM python:3.9-slim

WORKDIR /app

COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

COPY . .

# Tạo thư mục storage và cấp quyền ghi
RUN mkdir -p /app/storage && \
    chmod 777 /app/storage

# Tạo entrypoint script để đảm bảo quyền ghi mỗi khi container khởi động
RUN echo '#!/bin/bash\nchmod -R 777 /app/storage\nexec "$@"' > /entrypoint.sh && \
    chmod +x /entrypoint.sh

EXPOSE 8000

ENTRYPOINT ["/entrypoint.sh"]
CMD ["uvicorn", "main:app", "--host", "0.0.0.0", "--port", "8000"] 