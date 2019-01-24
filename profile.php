<?php include("includes/header.php"); ?>

<?php 
if (!$session->is_signed_in()) {redirect("login.php");}
?>

<?php  

/*
 * Decides wheter or not the profile is the one of the logged in user.
 * Will load in the profile_conent for either the signed in user or a 
 * other user.
*/

if ($session->user_id == $_GET['id']) {
  include("includes/self_profile_content.php"); 
  
} elseif (isset($_GET['id'])) {
  include("includes/other_profile_content.php"); 

} else {
	redirect("logout.php");

}

?>

<?php include("includes/footer.php"); ?>