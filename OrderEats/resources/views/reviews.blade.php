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
    <div class="container">
        <h2>Bảng Thông Tin Đánh Giá Shipper</h2>
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addModal">Thêm đánh giá</button>
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addModal"> đánh giá</button>
        <hr>
        <table id="myTable" class="display">
            <thead>
                <tr>
                    <th>Người Đặt:</th>
                    <th>Người Ship:</th>
                    <th>Đánh Giá:</th>
                    <th>Bình Luận:</th>
                    <th>Ngày Đánh Giá:</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
              @foreach($reviews as $item)
                <tr>
                    <th>{{$item->orders->user->fullname}}</th>
                    <th>{{$item->orders->user->fullname}}</th>
                    <th>{{$item->rating}} <i class="bi bi-star-fill"></i></th>
                    <th>{{$item->comment}}</th>
                    <th>{{$item->date}}</th>
                    <th>
                        <a type="submit" href="/reviews/{{$item->id}}" data-toggle="modal" data-target="#addModal">Edit</a>
                    </th>
                </tr>
              @endforeach
            </tbody>
        </table>
    </div>

    <!-- Add Modal -->
    <div class="modal" id="addModal">
        <form action="/reviews" method="POST" enctype="multipart/form-data">
            <div>
                <label for="">Đơn Hàng:</label>
                <select name="order_id">
                    @foreach($orders as $order)
                        <option value="{{ $order->id }}">{{ $order->id }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="">Đánh Giá:</label>
                <input type="number" name="rating">
            </div>
            <div>
                <label for="">Bình Luận:</label>
                <input type="text" name="comment">
            </div>
            <button type="submit" class="btn btn-primary">Lưu</button>
        </form>
    </div>

    <!-- Edit Modal -->
    <div class="modal" id="editModal">
        
    </div>

    <!-- Delete Modal -->
    <div class="modal" id="deleteModal">
        <!-- Delete Modal Content -->
    </div>

    <script>
        $(document).ready(function() {
            $('#myTable').DataTable();
        });
    </script>
</body>
</html>