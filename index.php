<?php
include_once('header.php');
?>
<html>
<head>
	<title> BuybySearch </title>
	<link rel="stylesheet" href="css/style.css" />
	<link rel="stylesheet" href="css/calendar.css" />
	<link rel="stylesheet" href="css/home.css" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<style>
		input[type=text] {
		width: 130px;
		box-sizing: border-box;
		border: 2px solid #ccc;
		border-radius: 3px;
		font-size: 16px;
		background-color: white;
		background-image: url('search.png');
		background-position: 10px 10px; 
		background-repeat: no-repeat;
		padding: 12px 20px 12px 40px;
		-webkit-transition: width 0.4s ease-in-out;
		transition: width 0.4s ease-in-out;
	}

	input[type=text]:focus {
		width: 100%;
	}
	
	* {box-sizing: border-box}
	body {font-family: Verdana, sans-serif; margin:0}
	img {vertical-align: middle;}
	
	table.d {
		table-layout: fixed;
		width: 100%;  
		high: 5%;
	}
	</style>
</head>
<body>

<!-- <div class="navbar">
	<b><img src="minilogo.png"></b>
	<a href="index.php">Home</a>
	<a href="aboutus.php">About us</a>
	<a href="service.php">Service</a>
	<a href="product.php">Product</a>
	<a href="information.php">Information</a>
	<div class="navbar-right">
		<b><form><input type="text" name="search" placeholder="Search.."></form></b>
		<a href="cart.php"><img src="cart.png"></a> 
		<div class="dropdown">
			<button class="dropbtn"><img src="setting.png"> 
			<i class="fa fa-caret-down"></i>
			</button>
			<div class="dropdown-content" >
				<a href="#">language</a>
				<a href="#">login</a>
			</div>
		</div>
	</div> 
</div> -->

<?php include_once('header.php'); ?>
<div id="cover"></div>
<div id="msg-cover"></div>
<!--  Begin of Content (Body)	-->
<div id="mainbody">
<div id="homepage">
<!-- Refer to w3schools (https://www.w3schools.com/howto/howto_js_slideshow.asp)>  -->
<!-- Refer to w3schools (https://www.w3schools.com/howto/howto_css_blurred_background.asp) -->


<div class="slideshow-container">

<div class="mySlides fade" style='text-align:center;margin:auto; vertical-align:middle;'>
  <div class="numbertext">1 / 3</div>
  <img src="images/slide1.jpg" style='display: block;margin:0 auto;max-width:100%;height:100%;'>
  <div class="text">An amazing car that are being sell!</div>
</div>

<div class="mySlides fade" style='text-align:center;margin:auto; vertical-align:middle;'>
  <div class="numbertext">2 / 3</div>
  <img src="images/slide2.jpg" style='display: block;margin:0 auto;max-width:100%;height:100%;'>
  <div class="text">Look for you favourite!</div>
</div>

<div class="mySlides fade" style='text-align:center;margin:auto; vertical-align:middle;'>
  <div class="numbertext">3 / 3</div>
  <img src="images/slide3.jpg" style='display: block;margin:0 auto;max-width:100%;height:100%;'>
  <div class="text">Explore a whole new world!</div>
</div>

<a class="prev" onclick="plusSlides(-1)">&#10094;</a>
<a class="next" onclick="plusSlides(1)">&#10095;</a>

<br><br>

<div style="text-align:center">
  <span class="dot" onclick="currentSlide(1)"></span> 
  <span class="dot" onclick="currentSlide(2)"></span> 
  <span class="dot" onclick="currentSlide(3)"></span> 
</div>

</div>

<script>
var slideIndex = 1;
var slideIndex = 1;
  setTimeout(showSlides, 0,1);
  const interval = setInterval(function() {
    plusSlides(1);
  }, 5000);

function plusSlides(n) {
  showSlides(slideIndex += n);
}

function currentSlide(n) {
  showSlides(slideIndex = n);
}

function showSlides(n) {
  var i;
  if(!document.getElementsByClassName("mySlides")){
    return;
  }
  var slides = document.getElementsByClassName("mySlides");
  var dots = document.getElementsByClassName("dot");
  if (n > slides.length) {slideIndex = 1}    
  if (n < 1) {slideIndex = slides.length}
  for (i = 0; i < slides.length; i++) {
      slides[i].style.display = "none";  
  }
  for (i = 0; i < dots.length; i++) {
      dots[i].className = dots[i].className.replace(" active", "");
  }
  slides[slideIndex-1].style.display = "block";  
  dots[slideIndex-1].className += " active";
}
</script>
<table class="d">
  <tr>
    <th><a onclick="submit_search_type('Product')">
    <img src="images/slide1.jpg" alt="Product" style="width:100%"></a></th>
    <th><a onclick="submit_search_type('Service')">
    <img src="images/slide2.jpg" alt="Service" style="width:100%"></a></th> 
    <th><a onclick="submit_search_type('Information')">
    <img src="images/slide3.jpg" alt="Information" style="width:100%"></a></th>
	<th>

<div class="jzdbox1 jzdbasf jzdcal">

<div class="jzdcalt">December</div>

<span>Sun</span>
<span>Mon</span>
<span>Tue</span>
<span>Wed</span>
<span>Thu</span>
<span>Fri</span>
<span>Sat</span>


<span class="jzdb"><!--BLANK--></span>
<span class="jzdb"><!--BLANK--></span>
<span class="jzdb"><!--BLANK--></span>
<span class="jzdb"><!--BLANK--></span>
<span class="jzdb"><!--BLANK--></span>
<span class="jzdb"><!--BLANK--></span>
<span>1</span>
<span>2</span>
<span>3</span>
<span>4</span>
<span>5</span>
<span>6</span>
<span>7</span>
<span>8</span>
<span>9</span>
<span>10</span>
<span>11</span>
<span class="circle" data-title="2 Years Anniversary!">12</span>
<span>13</span>
<span>14</span>
<span>15</span>
<span>16</span>
<span>17</span>
<span>18</span>
<span>19</span>
<span>20</span>
<span>21</span>
<span>22</span>
<span>23</span>
<span>24</span>
<span class="circle" data-title="Christmas Sales!">25</span>
<span>26</span>
<span>27</span>
<span>28</span>
<span>29</span>
<span>30</span>
<span class="jzdb"><!--BLANK--></span>
<span class="jzdb"><!--BLANK--></span>
<span class="jzdb"><!--BLANK--></span>
<span class="jzdb"><!--BLANK--></span>
<span class="jzdb"><!--BLANK--></span>
<span class="jzdb"><!--BLANK--></span>
</div>
</th>
</tr>
</table>
</div>
</div>
 
 <!--  End of Content (Body)	-->

<?php include_once('footer.php') ?>


<!-- Script -->
<script>

</script>
<!-- Script-->

</body>
</html>