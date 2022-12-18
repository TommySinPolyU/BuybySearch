<?php
include_once('header.php');
?>
<html>
<head>
	<title> BuybySearch </title>
</head>
<body>
<?php include_once('header.php'); ?>
<div id="cover"></div>
<div id="msg-cover"></div>
<!--  Begin of Content (Body)	-->
<div id="mainbody">
<!-- Refer to w3schools (https://www.w3schools.com/howto/howto_js_slideshow.asp)>  -->
<!-- Refer to w3schools (https://www.w3schools.com/howto/howto_css_blurred_background.asp) -->
<div class="bg-image"></div>
<div class="slideshow-container">

<div class="mySlides fade">
  <div class="numbertext">1 / 3</div>
  <img src="images/slide1.jpg" style="width:100%">
  <div class="text">An amazing car that are being sell!</div>
</div>

<div class="mySlides fade">
  <div class="numbertext">2 / 3</div>
  <img src="images/slide2.jpg" style="width:100%">
  <div class="text">Caption Two</div>
</div>

<div class="mySlides fade">
  <div class="numbertext">3 / 3</div>
   <img src="images/slide3.jpg" style="width:100%">
  <div class="text">Caption Three</div>
</div>

<a class="prev" onclick="plusSlides(-1)">&#10094;</a>
<a class="next" onclick="plusSlides(1)">&#10095;</a>

<div style="text-align:center">
  <span class="dot" onclick="currentSlide(1)"></span>
  <span class="dot" onclick="currentSlide(2)"></span>
  <span class="dot" onclick="currentSlide(3)"></span>
</div>
</div>
<br>

<br>
<table class="d">
  <tr>
    <!-- <th><a href="Product.php">
    <img src="slide2.jpg" alt="Forest" style="width:100%"></a></th>
    <th><a href="Information.php">
    <img src="slide3.jpg" alt="Mountains" style="width:100%"></a></th> 
    <th><a href="Service.php">
    <img src="slide1.jpg" alt="Snow" style="width:100%"></a></th>-->
	<th><div class="jzdbox1 jzdbasf jzdcal">
<!-- Refer to Calendar Mock(CSS only) (https://codepen.io/jamiemggs/pen/xdvaJv)> -->
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
</div></th>
  </tr>
</table>

</div>
<!--  End of Content (Body)	-->

<?php include_once('footer.php') ?>


<!-- Script -->
<script>

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
<!-- Script-->

</body>
</html>