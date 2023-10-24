      <!DOCTYPE html>
      <html>
      <head>
      <title>Trang Đăng Ký</title>
      <script src="https://www.gstatic.com/firebasejs/8.0.0/firebase-app.js"></script>
      <script src="https://www.gstatic.com/firebasejs/8.0.0/firebase-messaging.js"></script> 
      <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
</head>
      <body>
      <div class="container">
      <div class="row justify-content-center">
            <div class="col-md-6">
                  <div class="card">
                  <div class="card-header">Đăng Ký</div>
                  <div class="card-body">
                        <form method="POST" action="/api/register">
                              
                              <div class="form-group">
                              <label for="username">Tên đăng nhập</label>
                              <input type="text" class="form-control" id="username" name="username" required>
                              </div>
                              <div class="form-group">
                              <label for="email">Email</label>
                              <input type="email" class="form-control" id="email" name="email" required>
                              </div>
                              <div class="form-group">
                              <label for="password">Mật khẩu</label>
                              <input type="password" class="form-control" id="password" name="password" required>
                              </div>
                              <div class="form-group">
                                    <input type="hidden" id="fcm_token" name="fcm_token">
                              </div>
                              <div class="form-group">
                              <label for="password_confirmation">Xác nhận mật khẩu</label>
                              <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                              </div>
                              <button type="submit" class="btn btn-primary">Đăng Ký</button>
                        </form>
                  </div>
                  </div>
            </div>
      </div>
      </div>
      </body>
      <script>
  // Khởi tạo Firebase với thông tin cấu hình của bạn
  const firebaseConfig = {
  apiKey: "AIzaSyDxjjBJTK4afCNYAiUTAtOhGPr7IXIYRnA",
  authDomain: "ordereatslumen.firebaseapp.com",
  databaseURL: "https://ordereatslumen-default-rtdb.firebaseio.com",
  projectId: "ordereatslumen",
  storageBucket: "ordereatslumen.appspot.com",
  messagingSenderId: "823685759816",
  appId: "1:823685759816:web:1ecf2ac9d8f37d17d3697d",
  measurementId: "G-XWZ1CMRNXZ"
};

  firebase.initializeApp(firebaseConfig);

  // Lấy FCM token khi trang web đã tải hoàn thành
  document.addEventListener('DOMContentLoaded', function() {
    const messaging = firebase.messaging();
    messaging.getToken().then((token) => {
      if (token) {
        // token là FCM token
        console.log("FCM token:", token);
        // Đặt giá trị token vào một trường ẩn để sau này gửi lên máy chủ
        document.getElementById('fcm_token').value = token;
      } else {
        console.log("Không thể lấy FCM token.");
      }
    });
  });
</script>


      </html>
<?php /**PATH C:\xampp\htdocs\DuAnThucTap_TP-CDS\OrderEats\resources\views/register.blade.php ENDPATH**/ ?>