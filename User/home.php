<?php
    session_start();
    include("../connection/conn.php");

    if ($_SERVER['REQUEST_METHOD'] == "POST") {

        if (isset($_POST['addPost'])) {

            $sqlTitle = htmlspecialchars($_POST['sqlTitle'], ENT_QUOTES, 'UTF-8');
            $sqlDescription = htmlspecialchars($_POST['sqlDescription'], ENT_QUOTES, 'UTF-8');
            $sqlCode = htmlspecialchars($_POST['sqlCode'], ENT_QUOTES, 'UTF-8');
            
            $post_by_id = $_SESSION['id'];
            $status = 'Approved';

           
            $sqlInsertPost = "INSERT INTO sqlcommunity_interaction.post (post_by, title, description, code, status) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sqlInsertPost);
            $stmt->bind_param("issss", $post_by_id, $sqlTitle, $sqlDescription, $sqlCode, $status);


            $action_user_id = $post_by_id;
            $sqlGetNotificationInformation = "SELECT * FROM sqlcommunity_main.user_account WHERE id = $action_user_id";
            $queryGetNotificationInformation = mysqli_query($conn,$sqlGetNotificationInformation);
            $resultGetInformation = mysqli_fetch_assoc($queryGetNotificationInformation);


          $message = "You have successfully posted an SQL problem, detailing the specific issue or query that requires assistance or clarification.";

            
            
            $insertNotification = "INSERT INTO sqlcommunity_notifications.user_notification (user_id,notification) VALUES ('$action_user_id','$message')";
            mysqli_query($conn,$insertNotification);
         
            if ($stmt->execute()) {
                header("Location: home.php");
                exit();
            } else {
                echo "Error: " . $stmt->error;
            }

            $message = '';

          
            $stmt->close();
        }




        if(isset($_POST['commentForm'])){
                    $post_id = $_POST['post_id'];
                    $commenter_id = $_SESSION['id'];
                    
                
                    $comment = htmlspecialchars($_POST['textCommentInput'], ENT_QUOTES, 'UTF-8');
                    $code = htmlspecialchars($_POST['codeCommentInput'], ENT_QUOTES, 'UTF-8');


                    $insertComment = "INSERT INTO sqlcommunity_interaction.comments (post_id,commenter_id,comment,code) VALUES ('$post_id','$commenter_id','$comment','$code')";
                    mysqli_query($conn,$insertComment);

                    $message = "have posted a solution that provides detailed information and practical steps to effectively resolve the issue at hand.";


                    $insertCommentNotification = "INSERT INTO sqlcommunity_notifications.user_notification (user_id,notification) VALUES ('$commenter_id','$message')";
                    mysqli_query($conn,$insertCommentNotification);

                    $message = '';


                    

        }

        if(isset($_POST['reply'])){
                $comment_id = $_POST['comment_id'];
                $comment_by = $_SESSION['id'];
                $reply_value = $_POST['reply_value'];
                $post_id = $_POST['post_id'];

                $insertReplies = "INSERT INTO sqlcommunity_interaction.replies (post_id,comment_id,reply_by,message) VALUES ('$post_id','$comment_id','$comment_by','$reply_value')";
                mysqli_query($conn,$insertReplies);

        }
    }


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>SQL Comunity Management System</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    
    <link href="img/favicon.ico" rel="icon">

  
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700&display=swap" rel="stylesheet">
    
   
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

   
    <link href="template/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="template/lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

   
    <link href="template/css/bootstrap.min.css" rel="stylesheet">

   
    <link href="template/css/style.css" rel="stylesheet">
    
</head>

<body>
    <div class="container-xxl position-relative bg-white d-flex p-0">
      
        <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
       

        <div class="sidebar pe-4 pb-3">
            <nav class="navbar bg-light navbar-light">
                <a href="index.html" class="navbar-brand mx-4 mb-3">
                    <h3 class="text-primary">SQL Community</h3>
                </a>
                <div class="d-flex align-items-center ms-4 mb-4">
                    <div class="position-relative">
                        <img class="rounded-circle" src="<?php  echo $_SESSION['profile_picture']; ?>" alt="" style="width: 40px; height: 40px;">
                        <div class="bg-success rounded-circle border border-2 border-white position-absolute end-0 bottom-0 p-1"></div>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-0"><?php  echo $_SESSION['fullname']; ?></h6>
                        <span>User</span>
                    </div>
                </div>
                <div class="navbar-nav w-100">
                        <a href="dashboard.php" class="nav-item nav-link ">
                            <i class="fa fa-chart-line me-2"></i>Dashboard
                        </a>
                        <a href="home.php" class="nav-item nav-link active  ">
                            <i class="fa fa-home me-2"></i>Home
                        </a>
                        <a href="profile.php" class="nav-item nav-link ">
                            <i class="fa fa-user me-2"></i>Profile
                        </a>
                        <a href="notification.php" class="nav-item nav-link ">
                            <i class="fa fa-bell me-2"></i>Notification
                        </a>           
                </div>
            </nav>
        </div>
  


      
        <div class="content">
          
            <nav class="navbar navbar-expand bg-light navbar-light sticky-top px-4 py-0">
              
                <a href="#" class="sidebar-toggler flex-shrink-0">
                    <i class="fa fa-bars"></i>
                </a>
                <?php
              $searchTerm = '';
                ?>
                <form class="d-none d-md-flex ms-4" method="GET" action="">
                    <input class="form-control border-0" type="search" name="search" placeholder="Search" value="<?php echo htmlspecialchars($searchTerm); ?>">
                    <button class="btn btn-primary" type="submit" style = "margin-left: 10px;">Search</button>
                </form>
                <div class="navbar-nav align-items-center ms-auto">
                  
                     <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fa fa-bell me-lg-2"></i>
                            <span class="d-none d-lg-inline-flex">Notification</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0">

                            <?php 

                        date_default_timezone_set('Asia/Manila');
                                      $unique_id = $_SESSION['id'];
                                      $sqlRecentNotif = "SELECT * FROM sqlcommunity_notifications.user_notification WHERE user_id = $unique_id ORDER BY id DESC LIMIT 3";
                                      $Notifquery = mysqli_query($conn,$sqlRecentNotif);

                                      while($getNotif = mysqli_fetch_assoc($Notifquery)){


                                        $date_now = new DateTime(); 

                                        
                                        $date_post = new DateTime($getNotif['date']); 
                
                                    
                
                                        
                                        $interval = $date_now->diff($date_post);
                
                                    
                                        
                                        if ($interval->y > 0) {
                                            $timeString = $interval->y . ' year' . ($interval->y > 1 ? 's' : '') . ' ago';
                                        } elseif ($interval->m > 0) {
                                            $timeString = $interval->m . ' month' . ($interval->m > 1 ? 's' : '') . ' ago';
                                        } elseif ($interval->d > 0) {
                                            $timeString = $interval->d . ' day' . ($interval->d > 1 ? 's' : '') . ' ago';
                                        } elseif ($interval->h > 0) {
                                            $timeString = $interval->h . ' hour' . ($interval->h > 1 ? 's' : '') . ' ago';
                                        } elseif ($interval->i > 0) {
                                            $timeString = $interval->i . ' minute' . ($interval->i > 1 ? 's' : '') . ' ago';
                                        } else {
                                            $timeString = 'Just now';
                                        }

                                      
                            ?>
                          
                            <a href="notification.php" class="dropdown-item">
                                <h6 class="fw-normal mb-0"><?php 

                                            if($getNotif['type'] == 1){
                                                    echo "Posted a Problem";
                                            }else if($getNotif['type'] == 2){
                                                    echo "Solved a Problem";
                                            }
                                        
                                ?></h6>
                                <small><?php echo $timeString; ?></small>
                            </a>
                            <hr class="dropdown-divider">

                            <?php  }  ?>

                            <a href="notification.php" class="dropdown-item text-center">See all notifications</a>
                           
                        </div>
                    </div>
                    <div class="nav-item dropdown">
                       <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <img class="rounded-circle me-lg-2" src="<?php  echo $_SESSION['profile_picture']; ?>" alt="" style="width: 40px; height: 40px;">
                            <span class="d-none d-lg-inline-flex">Settings</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0">
                           <a href="home.php" class="dropdown-item">Home</a>
                            <a href="profile.php" class="dropdown-item">My Profile</a>
                            <a href="../index.php" class="dropdown-item">Log Out</a>
                        </div>
                    </div>
                </div>
            </nav>
         
            <div class="container-fluid pt-4 px-4">
                    <section>
                        
                            <!-- Button to trigger the modal -->
                            <div class="container mt-5">
                                <!-- Button to trigger the modal -->
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#sqlModal" style = "margin-top: -60px;">
                                    Post SQL Problem / Question
                                </button>
                            </div>

                            <!-- Modal for SQL Question Form -->
                            <div class="modal fade" id="sqlModal" tabindex="-1" aria-labelledby="sqlModalLabel" aria-hidden="true" >
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content" style = "border-radius: 20px;">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="sqlModalLabel">Ask a SQL Question</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- SQL Question Form Inside the Modal -->
                                            <form action="home.php" method="POST" enctype="multipart/form-data">
                                                <!-- SQL Title Input -->
                                                <div class="mb-3">
                                                    <label for="sqlTitle" class="form-label">SQL Question Title</label>
                                                    <input type="text" name="sqlTitle" id="sqlTitle" placeholder="Enter the SQL question title" class="form-control" required>
                                                </div>

                                                <!-- SQL Description Textarea -->
                                                <div class="mb-3">
                                                    <label for="sqlDescription" class="form-label">Describe Your Issue</label>
                                                    <textarea name="sqlDescription" id="sqlDescription" placeholder="Provide details about your SQL issue..." rows="3" class="form-control" required></textarea>
                                                </div>

                                                <!-- SQL Code Block Section -->
                                                <div class="mb-3">
                                                    <label for="sqlCode" class="form-label">SQL Code (optional)</label>
                                                    <textarea name="sqlCode" id="sqlCode" placeholder="Paste your SQL code here..." rows="3" class="form-control"></textarea>
                                                </div>

                                                <!-- Submit Button -->
                                                <div class="submit-container mt-3">
                                                    <input type="submit" name="addPost" value="Submit Post" class="btn btn-outline-success">
                                                </div>

                                                <style>
                                                    .submit-container {
                                                                text-align: right;  
                                                            }

                                                            .submit-container .btn {
                                                                display: inline-block;  
                                                            }

                                                </style>

                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>



                            <style>
                             
                                        .modal.fade .modal-dialog {
                                            transform: translateY(-50px);
                                            opacity: 0;
                                            transition: all 0.3s ease-in-out;
                                        }

                                        .modal.fade.show .modal-dialog {
                                            transform: translateY(0);
                                            opacity: 1;
                                        }


                                        .modal-content {
                                            animation: scaleUp 0.3s ease-in-out;
                                        }

                                        @keyframes scaleUp {
                                            0% {
                                                transform: scale(0.9);
                                                opacity: 0;
                                            }
                                            100% {
                                                transform: scale(1);
                                                opacity: 1;
                                            }
                                        }


                                        .btn-success {
                                            transition: background-color 0.3s, transform 0.2s;
                                        }

                                        .btn-success:hover {
                                            background-color: #28a745;
                                            transform: scale(1.05); 
                                        }

                            </style>
                    </section>



                    <section style="margin-top: 0px;">
                                <div class="post-list">



                                  <!-- post -->
                                  <?php

                                        date_default_timezone_set('Asia/Manila');

                                      
                                            if (isset($_GET['search'])) {
                                                $searchTerm = $conn->real_escape_string($_GET['search']); // Sanitize input
                                            }


                                            $sqlGetInfo = "SELECT * FROM sqlcommunity_interaction.post WHERE status = 'Approved'";
                                            if (!empty($searchTerm)) {
                                                $sqlGetInfo .= " AND (title LIKE '%$searchTerm%' OR description LIKE '%$searchTerm%' OR code LIKE '%$searchTerm%' OR post_date LIKE '%$searchTerm%')";
                                            }
                                            $sqlGetInfo .= " ORDER BY id DESC"; // Add sorting

                                            $queryGetInfo = mysqli_query($conn, $sqlGetInfo);

                                        while($getData = mysqli_fetch_assoc($queryGetInfo)) {
                                            $postId = $getData['id'];
                                        
                                            $user_id = $getData['post_by'];
                                            $sqlGetUserInfo = "SELECT * FROM sqlcommunity_main.user_account WHERE id = $user_id";
                                            $queryGetUserInfo = mysqli_query($conn, $sqlGetUserInfo);
                                            $resultUserInfo = mysqli_fetch_assoc($queryGetUserInfo);

                                        
                                            $date_now = new DateTime(); 

                                        
                                            $date_post = new DateTime($getData['post_date']); 

                                        

                                            
                                            $interval = $date_now->diff($date_post);

                                        
                                            
                                            if ($interval->y > 0) {
                                                $timeString = $interval->y . ' year' . ($interval->y > 1 ? 's' : '') . ' ago';
                                            } elseif ($interval->m > 0) {
                                                $timeString = $interval->m . ' month' . ($interval->m > 1 ? 's' : '') . ' ago';
                                            } elseif ($interval->d > 0) {
                                                $timeString = $interval->d . ' day' . ($interval->d > 1 ? 's' : '') . ' ago';
                                            } elseif ($interval->h > 0) {
                                                $timeString = $interval->h . ' hour' . ($interval->h > 1 ? 's' : '') . ' ago';
                                            } elseif ($interval->i > 0) {
                                                $timeString = $interval->i . ' minute' . ($interval->i > 1 ? 's' : '') . ' ago';
                                            } else {
                                                $timeString = 'Just now';
                                            }

                                        
                                            ?>
                                            <div class="post-item">
                                                <div class="post-header">
                                                    <div class="user-info">
                                                        <img src="<?php echo $resultUserInfo['profile_picture']; ?>" alt="User Profile Picture" class="user-profile-pic">
                                                        <div class="user-name">
                                                            <strong><?php echo $resultUserInfo['fullname']; ?></strong>
                                                            <span class="post-time">• <?php echo $timeString; ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="post-content">
                                                    <h2 class="post-title"><?php echo $getData['title']; ?></h2>
                                                    <p><?php echo $getData['description']; ?></p>
                                                
                                                    <pre class="code-block">
                                                        <code><?php echo $getData['code']; ?></code>
                                                    </pre>
                                                    <p>Is this the right way to prevent SQL problems?</p>
                                                </div>
                                              <div class="actions" style = "display: flex; ">
                                                 <div class="add_comments">
                                               

                                                 <button class="comment-button btn btn-primary" style="background-color: #78B3CE; color: white;" data-bs-toggle="modal" data-bs-target="#commentModal<?php echo $postId; ?>">
                                                    Post a Solution
                                                </button>

                                                <form action="home.php" method = "post">
                                                    <div class="modal fade" id="commentModal<?php echo $postId; ?>" tabindex="-1" aria-labelledby="commentModalLabel<?php echo $postId; ?>" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header" style="background-color: #78B3CE;">
                                                                    <h5 class="modal-title" id="commentModalLabel<?php echo $postId; ?>" style="color: white;">Notification!</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="color: white;"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <h6>Text Comment</h6>
                                                                    <textarea id="textCommentInput" name = "textCommentInput" class="form-control" placeholder="Enter your text comment..." style="width: 100%; height: 100px;"></textarea>
                                                                    <hr>
                                                                    <h6>Code Comment</h6>
                                                                    <textarea id="codeCommentInput" name = "codeCommentInput" class="form-control" placeholder="Enter your code..." style="width: 100%; height: 150px; font-family: 'Courier New', monospace; background-color: #f4f4f4;"></textarea>
                                                                    <input type="hidden" name="post_id" value="<?php echo $postId; ?>">
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal" aria-label="Close" style="border-radius: 20px;">Close</button>
                                                                    <input type="submit" name="commentForm" value="Submit Comment" class="btn btn-outline-success" style="border-radius: 20px;">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>

                                                                

                                                             
                                                </div>
                                                   <div class="comment_link" style = "margin-left: 10px;">
                                                        
                                                   <?php  
                                                            $selectComments = "SELECT COUNT(*) AS comment_count FROM sqlcommunity_interaction.comments WHERE post_id = '$postId'";
                                                            $queryComments = mysqli_query($conn, $selectComments);
                                                            $resultComments = mysqli_fetch_assoc($queryComments);
                                                            $commentCount = $resultComments['comment_count']; 

                                                            $displayCount = ($commentCount > 99) ? "99+" : $commentCount;
                                                        ?>

                                                        <div style="position: relative; display: inline-block;">
                                                            <button class="comment-button" 
                                                                    style="background-color: #62825D; color: white;" 
                                                                    onclick="toggleComments(<?php echo $postId; ?>)"
                                                                    <?php echo $commentCount == 0 ? 'disabled' : ''; ?>>
                                                                💬 Show Solutions
                                                            </button>
                                                            
                                                            <!-- Comment Badge (displays 0 when no comments) -->
                                                            <span class="comment-badge"><?php echo $displayCount; ?></span>
                                                        </div>

                                                        


                                                        

                                                    <!-- CSS for Badge -->
                                                    <style>
                                                       .comment-badge {
                                                                position: absolute;
                                                                top: -5px;
                                                                right: -4px;
                                                                background-color: red;
                                                                color: white;
                                                                padding: 2px 5px;
                                                                border-radius: 50%;
                                                                font-size: 0.8em;
                                                                font-weight: bold;
                                                                width: 20px;
                                                                height: 20px;
                                                                display: flex;
                                                                align-items: center;
                                                                justify-content: center;
                                                            }
                                                    </style>

                                                            <script>
                                                            
                                                                function toggleComments(postId) {
                                                                
                                                                    var commentsSection = document.getElementById('comments-' + postId);
                                                                    
                                                                
                                                                    if (commentsSection.style.display === "none" || commentsSection.style.display === "") {
                                                                        commentsSection.style.display = "block";
                                                                    } else {
                                                                        commentsSection.style.display = "none";
                                                                    }
                                                                }
                                                                </script>

                                                              
                                                        </div>
                                                </div>

                                                <!-- comments -->
                                                <div class="comments" id="comments-<?php echo $postId; ?>" style = "display: none;">
                                                         <ul class="timeline">
                                                         <?php
                                                                $getCommentByThisPost = "SELECT * FROM sqlcommunity_interaction.comments WHERE post_id = $postId ORDER BY id ASC";
                                                                $queryCommentByThisPost = mysqli_query($conn, $getCommentByThisPost);

                                                                while ($resultCommentByThisPost = mysqli_fetch_assoc($queryCommentByThisPost)) {
                                                                    $commentDateTime = new DateTime($resultCommentByThisPost['comment_date']); 
                                                                    $currentDateTime = new DateTime();
                                                                    
                                                                  
                                                                    $interval = $currentDateTime->diff($commentDateTime);

                                                                 
                                                                    if ($interval->days == 0) {
                                                                      
                                                                        $dateString = "today";
                                                                        $timeString = $commentDateTime->format("H:i"); 
                                                                    } else {
                                                                       
                                                                        $dateString = $commentDateTime->format("Y-m-d");
                                                                        $timeString = $commentDateTime->format("H:i");
                                                                    }
                                                            ?>

                                                            <li>
                                                                <div class="timeline-time">
                                                                    <span class="date" style="color: #003C43;"><?php echo $dateString; ?></span>
                                                                    <span class="time" style="color: #003C43;"><?php echo $timeString; ?></span>
                                                                </div>
                                                            </li>
                                                            
                                                                <div class="timeline-icon">
                                                                    <a href="javascript:;">&nbsp;</a>
                                                                </div>
                                                                
                                                                <div class="timeline-body" style = "width: 78%;box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.5); border-radius: 20px;">
                                                                    <div class="timeline-header">

                                                                            <?php
                                                                                $commemter_id = $resultCommentByThisPost['commenter_id'];
                                                                                    $getCommentInfo = "SELECT * FROM sqlcommunity_main.user_account WHERE id = $commemter_id";
                                                                                    $queryCommentInfo = mysqli_query($conn,$getCommentInfo);
                                                                                    $resultCommentInfo = mysqli_fetch_assoc($queryCommentInfo);
                                                                            ?>
                                                                        <span class="userimage"><img src="<?php echo $resultCommentInfo['profile_picture']; ?>" alt=""></span>
                                                                        <span class="username"><a href="javascript:;"><?php echo $resultCommentInfo['fullname']; ?></a> <small></small></span>
                                                                        
                                                                    </div>
                                                                    <div class="timeline-content" >
                                                                            <p><?php echo $resultCommentByThisPost['comment']; ?></p>
                                                        
                                                                            <pre class="code-blocks"><code><?php echo $resultCommentByThisPost['code']; ?></code></pre>
                                                                            
                                                                            <style>
                                                                             .code-blocks {
                                                                                    background: linear-gradient(135deg, #173B45, #343131);
                                                                                    color: white;
                                                                                    white-space: pre-wrap;   
                                                                                    word-wrap: break-word;  
                                                                                    overflow-x: hidden;      
                                                                                    font-family: 'Courier New', monospace; 
                                                                                    background-color: #f4f4f4;
                                                                                    padding: 10px;          
                                                                                    border-radius: 5px;    
                                                                                    text-align: left;   
                                                                                }


                                                                            </style>
                                                                            
                                                                         
                                                                        <div class="stats-right">
                                                                        <?php  
                                                                        $comment_id = $resultCommentByThisPost['id'];
                                                                                $selectComments = "SELECT COUNT(*) AS reply_count FROM sqlcommunity_interaction.replies WHERE post_id = '$postId' AND comment_id = '$comment_id'";
                                                                                $queryComments = mysqli_query($conn, $selectComments);
                                                                                $resultComments = mysqli_fetch_assoc($queryComments);
                                                                                $commentCount = $resultComments['reply_count']; 

                                                                                $displayCount = ($commentCount > 99) ? "99+" : $commentCount;
                                                                            ?>
                                                                               
                                                                                 <script>
                                                                                        
                                                                                        window.onload = function() {
                                                                                            var commentCount = document.getElementById('comment-count');  
                                                                                            var repliesDiv = document.querySelector('.replies'); 
                                                                                            
                                                                                           
                                                                                            commentCount.onclick = function() {
                                                                                                
                                                                                                if (repliesDiv.style.display === "none") {
                                                                                                    repliesDiv.style.display = "block"; 
                                                                                                } else {
                                                                                                    repliesDiv.style.display = "none";  
                                                                                                }
                                                                                            };
                                                                                        };
                                                                                    </script>


                                                                               <style>
                                                                                #comment-count {
                                                                                    cursor: pointer; 
                                                                                }

                                                                               </style>


                                                                        </div>

                                                                      

                                                                        <style>
                                                                            .stats-right {
                                                                                        float: right;
                                                                                        color: #78B3CE;
                                                                                        font-weight: bolder;
                                                                                    }

                                                                        </style>

                                                                    </div>
                                                                    <div class="timeline-likes">
                                                                       
                                                                        <div class="stats">
                                                                           
                                                                        </div>
                                                                    </div>
                                                                    
                                                                   

                                                                                           
                                                                    
                                                                            </li>
                                                                        </div>
                                                        
                                                              

                                                                 <?php  } ?>

                                                                <style>
                                                                    
                                                                    .timeline {
                                                                        list-style-type: none;
                                                                        margin: 0;
                                                                        padding: 0;
                                                                        position: relative
                                                                    }

                                                                    .timeline:before {
                                                                        content: '';
                                                                        position: absolute;
                                                                        top: 5px;
                                                                        bottom: 5px;
                                                                        width: 5px;
                                                                        background: #78B3CE;
                                                                        left: 20%;
                                                                        margin-left: -2.5px
                                                                    }

                                                                    .timeline>li {
                                                                        position: relative;
                                                                        min-height: 50px;
                                                                        padding: 20px 0
                                                                    }

                                                                    .timeline .timeline-time {
                                                                        position: absolute;
                                                                        left: 0;
                                                                        width: 18%;
                                                                        text-align: right;
                                                                        top: 30px
                                                                    }

                                                                    .timeline .timeline-time .date,
                                                                    .timeline .timeline-time .time {
                                                                        display: block;
                                                                        font-weight: 600
                                                                    }

                                                                    .timeline .timeline-time .date {
                                                                        line-height: 16px;
                                                                        font-size: 12px
                                                                    }

                                                                    .timeline .timeline-time .time {
                                                                        line-height: 24px;
                                                                        font-size: 20px;
                                                                        color: #242a30
                                                                    }

                                                                    .timeline .timeline-icon {
                                                                        left: 15%;
                                                                        position: absolute;
                                                                        width: 10%;
                                                                        text-align: center;
                                                                        top: 40px
                                                                    }

                                                                    .timeline .timeline-icon a {
                                                                        text-decoration: none;
                                                                        width: 20px;
                                                                        height: 20px;
                                                                        display: inline-block;
                                                                        border-radius: 20px;
                                                                        background: #d9e0e7;
                                                                        line-height: 10px;
                                                                        color: #fff;
                                                                        font-size: 14px;
                                                                        border: 5px solid #78B3CE;
                                                                        transition: border-color .2s linear
                                                                    }

                                                                    .timeline .timeline-body {
                                                                        margin-left: 23%;
                                                                        margin-right: 17%;
                                                                        background: #fff;
                                                                        position: relative;
                                                                        padding: 20px 25px;
                                                                        border-radius: 6px
                                                                    }

                                                                    .timeline .timeline-body:before {
                                                                        content: '';
                                                                        display: block;
                                                                        position: absolute;
                                                                        border: 10px solid transparent;
                                                                        border-right-color: #fff;
                                                                        left: -20px;
                                                                        top: 20px
                                                                    }

                                                                    .timeline .timeline-body>div+div {
                                                                        margin-top: 15px
                                                                    }

                                                                    .timeline .timeline-body>div+div:last-child {
                                                                        margin-bottom: -20px;
                                                                        padding-bottom: 20px;
                                                                        border-radius: 0 0 6px 6px
                                                                    }

                                                                    .timeline-header {
                                                                        padding-bottom: 10px;
                                                                        border-bottom: 1px solid #e2e7eb;
                                                                        line-height: 30px
                                                                    }

                                                                    .timeline-header .userimage {
                                                                        float: left;
                                                                        width: 34px;
                                                                        height: 34px;
                                                                        border-radius: 40px;
                                                                        overflow: hidden;
                                                                        margin: -2px 10px -2px 0
                                                                    }

                                                                    .timeline-header .username {
                                                                        font-size: 16px;
                                                                        font-weight: 600
                                                                    }

                                                                    .timeline-header .username,
                                                                    .timeline-header .username a {
                                                                        color: #2d353c
                                                                    }

                                                                    .timeline img {
                                                                        max-width: 100%;
                                                                        display: block
                                                                    }

                                                                    .timeline-content {
                                                                        letter-spacing: .25px;
                                                                        line-height: 18px;
                                                                        font-size: 13px
                                                                    }

                                                                    .timeline-content:after,
                                                                    .timeline-content:before {
                                                                        content: '';
                                                                        display: table;
                                                                        clear: both
                                                                    }

                                                                    .timeline-title {
                                                                        margin-top: 0
                                                                    }

                                                                    .timeline-footer {
                                                                        background: #fff;
                                                                        border-top: 1px solid #e2e7ec;
                                                                        padding-top: 15px
                                                                    }

                                                                    .timeline-footer a:not(.btn) {
                                                                        color: #575d63
                                                                    }

                                                                    .timeline-footer a:not(.btn):focus,
                                                                    .timeline-footer a:not(.btn):hover {
                                                                        color: #2d353c
                                                                    }

                                                                    .timeline-likes {
                                                                        color: #6d767f;
                                                                        font-weight: 600;
                                                                        font-size: 12px
                                                                    }

                                                                    .timeline-likes .stats-right {
                                                                        float: right
                                                                    }

                                                                    .timeline-likes .stats-total {
                                                                        display: inline-block;
                                                                        line-height: 20px
                                                                    }

                                                                    .timeline-likes .stats-icon {
                                                                        float: left;
                                                                        margin-right: 5px;
                                                                        font-size: 9px
                                                                    }

                                                                    .timeline-likes .stats-icon+.stats-icon {
                                                                        margin-left: -2px
                                                                    }

                                                                    .timeline-likes .stats-text {
                                                                        line-height: 20px
                                                                    }

                                                                    .timeline-likes .stats-text+.stats-text {
                                                                        margin-left: 15px
                                                                    }

                                                                    .timeline-comment-box {
                                                                        background: #f2f3f4;
                                                                        margin-left: -25px;
                                                                        margin-right: -25px;
                                                                        padding: 20px 25px
                                                                    }

                                                                    .timeline-comment-box .user {
                                                                        float: left;
                                                                        width: 34px;
                                                                        height: 34px;
                                                                        overflow: hidden;
                                                                        border-radius: 30px
                                                                    }

                                                                    .timeline-comment-box .user img {
                                                                        max-width: 100%;
                                                                        max-height: 100%
                                                                    }

                                                                    .timeline-comment-box .user+.input {
                                                                        margin-left: 44px
                                                                    }

                                                              
                                                                </style>
                                                        </ul>
                                                    </div>
                                                <!-- end sa comments -->

                                              
                                                <style>
                                                    .post-actions {
                                                        display: flex; 
                                                        gap: 10px;
                                                        justify-content: flex-start;
                                                    }

                                                    .like-button, .comment-button {
                                                        padding: 10px 20px;
                                                        border: none;
                                                        cursor: pointer;
                                                        font-size: 14px;
                                                        border-radius: 20px;
                                                    }

                                                    .like-button:hover, .comment-button:hover {
                                                        opacity: 0.9; /* Slightly change the button opacity on hover for better user interaction */
                                                    }
                                                </style>

                                            </div>
                                            <?php
                                        }
                                        ?>






                                </div>

                                

                             
                                <!-- end sa post -->

                                <style>
                              
                                body, html {
                                    margin: 0;
                                    padding: 0;
                                    width: 100%;
                                    height: 100%;
                                    background-color: #f4f4f4;
                                    font-family: Arial, sans-serif;
                                }

                              
                                .post-list {
                                    width: 100%;
                                    max-width: 100%;  /* Ensure it can take up the full width */
                                    margin: 0 auto;  /* Center the content horizontally */
                                    padding: 10px;
                                }

                                /* Individual post container */
                                .post-item {
                                    background-color: #fff;
                                    border-radius: 8px;
                                    box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
                                    margin-bottom: 20px;
                                    padding: 20px;
                                    width: 100%;
                                    border-left: 5px solid #0077b6;  /* Blue border for visual effect */
                                }

                                /* Header with user info */
                                .post-header {
                                    display: flex;
                                    justify-content: space-between;
                                    margin-bottom: 15px;
                                }

                                .user-info {
                                    display: flex;
                                    align-items: center;
                                }

                                .user-profile-pic {
                                    width: 50px;
                                    height: 50px;
                                    border-radius: 50%;
                                    margin-right: 10px;
                                }
                   


                                .user-name {
                                    display: flex;
                                    flex-direction: column;
                                }

                                .user-name strong {
                                    font-size: 16px;
                                    color: #333;
                                }

                                .user-name .post-time {
                                    font-size: 12px;
                                    color: grey;
                                }

                                /* Content of the post (text and code) */
                                .post-content {
                                    margin-bottom: 15px;
                                }

                                .post-title {
                                    font-size: 20px;
                                    font-weight: bold;
                                    color: #333;
                                    margin-bottom: 10px;
                                }

                                .post-content p {
                                    font-size: 14px;
                                    color: #333;
                                    margin-bottom: 10px;
                                }

                             

                                .code-block {
                                    background: linear-gradient(135deg, #173B45, #343131);
                                    border: 1px solid #ddd;
                                    padding: 10px;
                                    border-radius: 5px;
                                    overflow-x: auto;
                                    white-space: pre; /* Preserves line breaks and spaces but prevents extra indentation */
                                    font-family: "Courier New", monospace;
                                    font-size: 14px;
                                    color: white;
                                    margin: 10px 0;
                                }

                                .code-block code {
                                    display: block;
                                    margin: 0;
                                    padding: 0;
                                    white-space: pre; /* Ensures code wraps exactly as written */
                                }



                                /* Buttons for actions (Like, Comment, Share) */
                                .post-actions {
                                    display: flex;
                                    justify-content: space-between;
                                    margin-top: 15px;
                                }

                                .post-actions button {
                                    background-color: #f0f0f0;
                                    padding: 8px 16px;
                                    border: none;
                                    border-radius: 25px;
                                    cursor: pointer;
                                    font-size: 14px;
                                    color: #555;
                                    transition: background-color 0.3s ease;
                                }

                                .post-actions button:hover {
                                    background-color: #e0e0e0;
                                }

                                .like-button {
                                    color: #3b5998;
                                }

                                .comment-button {
                                    color: #8b9dc3;
                                }

                                .share-button {
                                    color: #45b3e0;
                                }
                                </style>
                            </section>


                      
                  </div>
          
                    <div class="container-fluid pt-4 px-4">
                        <div class="bg-light rounded-top p-4">
                            <div class="row">
                                <div class="col-12 col-sm-6 text-center text-sm-start">
                                    &copy; <a href="#">SQL Community Management System</a>, All Right Reserved. 
                                </div>
                                <div class="col-12 col-sm-6 text-center text-sm-end">
                                
                                    Designed By: <a href="#">Ejie C. Florida</a>
                                </div>
                            </div>
                        </div>
                    </div>
                
                </div>
    
                            <a href="#" class="btn btn-primary back-to-top">
                        <i class="bi bi-arrow-up"></i>
                        </a>

                        <style>
                        /* Back to Top Button Style (Circle) */
                        .back-to-top {
                            position: fixed;
                            bottom: 20px;
                            right: 20px;
                            width: 50px;
                            height: 50px;
                            border-radius: 50%;
                            background-color: #007bff; /* Blue background */
                            color: white;
                            display: flex;
                            justify-content: center;
                            align-items: center;
                            font-size: 24px;
                            text-decoration: none;
                            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
                            transition: background-color 0.3s;
                        }

                        /* Hover Effect */
                        .back-to-top:hover {
                            background-color: #0056b3; /* Darker blue */
                        }
                        </style>
                </div>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="template/lib/chart/chart.min.js"></script>
    <script src="template/lib/easing/easing.min.js"></script>
    <script src="template/lib/waypoints/waypoints.min.js"></script>
    <script src="template/lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="template/lib/tempusdominus/js/moment.min.js"></script>
    <script src="template/lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="template/template/lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

   
    <script src="template/js/main.js"></script>
</body>

</html>