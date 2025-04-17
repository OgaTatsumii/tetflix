<?php
function showAlert()
{
    if (isset($_SESSION['success']) || isset($_SESSION['error'])): ?>
<div class="alert-container">
    <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success">
        <span class="alert-text"><?php echo $_SESSION['success'];
                    unset($_SESSION['success']); ?></span>
        <button type="button" class="alert-close">&times;</button>
    </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger">
        <span class="alert-text"><?php echo $_SESSION['error'];
                    unset($_SESSION['error']); ?></span>
        <button type="button" class="alert-close">&times;</button>
    </div>
    <?php endif; ?>
</div>
<?php endif;
}
?>