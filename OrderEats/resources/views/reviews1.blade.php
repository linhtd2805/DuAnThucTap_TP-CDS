<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
<h1>Chi Tiết Bình Luận</h1>    

<form action="" method="POST" enctype="multipart/form-data">
    <div>
        <label for="">Người Đặt:</label>
        <input type="text" name="{{ $reviews->orders->user->fullname}}">
    </div>
    <div>
        <label for="">Người Ship:</label>
        <input type="text" name="{{ $reviews->orders->user->fullname}}">
    </div>
    <div>
        <label for="">Đánh Giá:</label>
        <input type="number" name="{{ $reviews->rating }}" value="{{ $reviews->rating }}">
    </div>
    <div>
        <label for="">Bình Luận:</label>
        <input type="text" value = "{{ $reviews->comment }}"> <br>
    </div>
    <div>
        <label for="">Ngày:</label>
        <input type="date" value = "{{ $reviews-> date }}">
    </div>
</form>
</body>
</html>