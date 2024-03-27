<?php
	if (isset($_SESSION['id'])) { 
		header("Location:login.php");
	}
	include_once("../includes/header.php");
    include_once("../includes/notification/Push.php");  
    $push = new Push();
?>
<body class="app"> 
	<?php include_once("../includes/sidepanel.php"); ?>  	
<div class="app-wrapper">
<div class="app-content pt-3 p-md-3 p-lg-5">
<div class="container-xl">
<div class="row g-3 mb-4 align-items-center justify-content-between">
<div class="col-auto">
<h1 class="app-page-title mb-0">Create New Classes</h1>
</div>
</div>
</div>			   
<div class="col-12 col-md-12 col-lg-12 auth-main-col text-center p-5">
<div class="d-flex flex-column align-content-end">
<div class="app-auth-body mx-auto col-10 col-md-10 col-lg-10">	
<div class="auth-form-container text-start ">
	<?php 	if (isset($_POST['submit'])) { 
		if(isset($_POST['msg']) and isset($_POST['time']) and isset($_POST['loops']) and isset($_POST['loop_every']) and isset($_POST['user'])) {
			$title = $_POST['title'];	
			$msg = $_POST['msg']; 
			$time = date('Y-m-d H:i:s'); 
			$loop= $_POST['loops']; 
			$loop_every=$_POST['loop_every']; 
			$user = $_POST['user']; 
            if(empty($title)){
				echo $message = '<div class="alert alert-danger" role="alert">Enter The Notification Title</div>';
            }else if(empty($msg)){
				echo $message = '<div class="alert alert-danger" role="alert">Enter The Notification Body</div>';
            }else if(empty($time)){
				echo $message = '<div class="alert alert-danger" role="alert">Enter The Broadcast time</div>';
            }else if(empty($loop)){
				echo $message = '<div class="alert alert-danger" role="alert">Enter Loop (time) Number </div>';
            }else if(empty($loop_every)){
				echo $message = '<div class="alert alert-danger" role="alert">Enter Loop Every (Minute)</div>';
            }else if(empty($user)){
				echo $message = '<div class="alert alert-danger" role="alert">Enter The User </div>';
            }else{
                $isSaved = $push->saveNotification($title, $msg,$time,$loop,$loop_every,$user);
                if($isSaved) {
                    echo $message = '<div class="alert alert-success" role="alert">Notification  Saved successfully</div>';
                } else {
                    echo $message = '<div class="alert alert-danger" role="alert">Notification  error save data</div>';
                }
            }
		} else {
			echo  $message = '<div class="alert alert-danger" role="alert">completed the parameter above</div>';
		}
	} 
    ?>
	<form action="" method="post"  class="form-horizontal form-align" enctype="multipart/form-data">
                    <div class="form-group">
                    <label class="control-label col-md-1 form_lable label-align" for="name"><b>إسم الصحفي  </b></label>
                        <div class="col-md-3">
                            <input type="text" name="name" class="form-control"  id="name" placeholder="أدخل إسم الصحفي ">
                        </div>
                        <label class="control-label col-md-1 form_lable label-align" for="editor_name"><b>إسم الدخول  </b></label>
                        <div class="col-md-3">
                            <input type="text" name="editor_name" class="form-control"  id="editor_name" placeholder="أدخل إسم الدخول ">
                        </div>
                        <label class="control-label col-md-1 form_lable label-align" for="editor_email"><b>الإيميل</b></label>                                            
                        <div class="col-md-3">
                            <input type="email" name="editor_email" class="form-control"  id="editor_email" placeholder="أدخل الإيميل ">
                        </div>                                     
                       
                    </div>

                    <div class="form-group">
                    <label class="control-label col-md-1 form_lable label-align" for="editor_gender"><b>النوع</b></label>                                            
                        <div class="col-md-2">
                            <select name="editor_gender"  class="form-control" id="editor_gender" >
                                <option value=""></option>
                                <option value="male"> ذكر</option>
                                <option value="femail">أنثى</option>
                            </select>
                        </div>
                        <label class="control-label col-sm-1 form_lable label-align" for="editor_avatar"> صورة الصحفي </label>
                        <div class="col-md-2">
                            <input type="file" name="editor_avatar" class="form-control" id="editor_avatar"  >
                        </div>                            
                        <label class="control-label col-sm-1 form_lable label-align" for="sections"> إختر قسم  </label>
                        <div class="col-md-2">
                            <select name="editor_department"  class="form-control" id="sections" >
                                <option value=""></option>
                                <?php 
                                $sql = mysqli_query($conn, "SELECT * FROM `sections`");
                                while($sec = mysqli_fetch_assoc($sql)){
                                echo '<option value="'.$sec['section_name'].'">'.$sec['section_description'].'</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <label class="control-label col-sm-1 form_lable label-align" for="editor_role"> الصلاحيــات  </label>
                        <div class="col-md-2">
                            <select name="editor_role"  class="form-control" id="editor_role" >
                                <option value=""></option>
                                <option value="admin"> رئيس التحرير</option>
                                <!-- <option value="editor_manager">رئيس التحرير</option> -->
                                <option value="article_manager">محرر قسم المقالات  </option>
                                <option value="article_editor">صحفي في قسم المقالات</option>
                                <option value="news_manager">محرر قسم الاخبار</option>
                                <option value="news_editor"> صحفي في قسم الاخبار</option>
                                <option value="mix_manager">محرر قسم المنوعات</option>
                                <option value="mix_editor"> صحفي في قسم المنوعات</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-1 form_lable label-align" for="password_1"><b>كلمة المرور</b></label>
                        <div class="col-md-4">
                            <input type="password" name="password_1" class="form-control"  id="password_1" placeholder="أدخل كلمة المرور">
                        </div>
                        <label class="control-label col-md-1 form_lable label-align" for="password_2"><b>تأكيد</b></label>                                            
                        <div class="col-md-4">
                            <input type="password" name="password_2" class="form-control"  id="password_2" placeholder="قم بتأكيد كلمة المرور">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-1 form_lable label-align" for="facebook"><b><i class="fa fa-facebook-square" style="font-size: 200%;"></i></b></label>
                        <div class="col-md-4">
                            <input type="text" name="facebook" class="form-control"  id="facebook" placeholder="أدخل رابط حساب على فيسبوك ">
                        </div>
                        <label class="control-label col-md-1 form_lable label-align" for="twitter"><b><i class="fa fa-twitter-square" style="font-size: 200%;"></i></b></label>                                            
                        <div class="col-md-4">
                            <input type="text" name="twitter" class="form-control"  id="twitter" placeholder="أدخل رابط حساب على تويتر ">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-1 form_lable label-align" for="about_editor"> التفاصيل   </label>
                        <div class="col-md-12">
                            <textarea name="about_editor" class="form-control tinymce"   id="about_editor" rows="8"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-8">
                        <input type="hidden" name="editor_status" value="block" />
                            <input type="submit"  name="add_editor" class="btn-hover color-3 col-md-4" value="إضافة محرر" style="font-size:100%;"/>
                        </div>
                    </div>
                </form>
<script type="text/javascript">
	const amountInput = document.getElementById('amountInput');
	amountInput.addEventListener('input', function () {
		const value = this.value;
		if (!/^(\d+(\.\d{1,2})?)?$/.test(value)) {
			this.setCustomValidity('Enter a valid decimal number (up to 2 decimal places)');
		} else {
			this.setCustomValidity('');
		}
	});
</script>
<!-- tinymce Text Editor -->
<script src="../assets/plugins/tinymce/tinymce.min.js"></script>
<script src="../assets/plugins/tinymce/init-tinymce.js"></script>
<?php
	include_once("../includes/footer.php");
?>