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
</head>
<body>
    <div class="container">
        <h2>Bảng Thông Tin Đánh Giá Shipper</h2>
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addModal">Add New</button>
        <hr>
        <table id="myTable" class="display">
            <thead>
                <tr>
                    <th>Người Đặt:</th>
                    <th>Người Ship:</th>
                    <th>Đánh Giá:</th>
                    <th>Bình Luận:</th>
                    <th>Ngày Đánh Giá:</th>
                </tr>
            </thead>
            <tbody>
              @foreach($reviews as $item)
                <tr>
                    <th>{{$item['user_id']}}</th>
                    <th>{{$item['user_id']}}</th>
                    <th>{{$item['rating']}}</th>
                    <th>{{$item['comment']}}</th>
                    <th>{{$item['date']}}</th>
                </tr>
              @endforeach
            </tbody>
        </table>
    </div>

    <!-- Add Modal -->
    <div class="modal" id="addModal">
        <input type="text">
    </div>

    <!-- Edit Modal -->
    <div class="modal" id="editModal">
        <!-- Edit Modal Content -->
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