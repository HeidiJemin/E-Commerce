<?php

include('./includes/connect.php');

function getprodukt() {
  global $con;
  if (!isset($_GET['liga'])) {
      if (!isset($_GET['ekip'])) {
          $select_query = "Select * from `produkt` order by rand() limit 0,9";
          $result_query = mysqli_query($con, $select_query);

          if (mysqli_num_rows($result_query) > 0) {
              while ($row = mysqli_fetch_assoc($result_query)) {
                  $produkt_id = $row['produkt_id'];
                  $produkt_name = $row['produkt_name'];
                  $produkt_description = $row['produkt_description'];
                  $produkt_image1 = $row['produkt_image1'];
                  $produkt_price = $row['produkt_price'];

                  echo "<div class='col-md-4'>
                          <div class='card'>
                              <div class='imgBox'>
                                  <img src='./admin_manage/produkt_image/$produkt_image1' alt='$produkt_name' class='mouse'>
                              </div>
                              <div class='contentBox'>
                                  <h3>$produkt_name</h3>
                                  <h2 class='price'>$produkt_price €</h2>
                                  <a href='produkt_info.php?produkt_id=$produkt_id' class='buy'>View More</a>
                              </div>
                          </div>
                        </div>";
              }
          } else {
              // Display a placeholder message when no products are found
              echo "<div class='text-center' style='width: 100%; padding: 20px;min-height:450px;'>
                      <h4>No products available at the moment.</h4>
                    </div>";
          }
      }
  }
}




function get_all_produkt(){
    global $con;
    if(!isset($_GET['liga'])){
        if(!isset($_GET['ekip'])){
      $select_query="Select * from `produkt` order by rand()";
      $result_query=mysqli_query($con,$select_query);
      while($row=mysqli_fetch_assoc($result_query)){
        $produkt_id=$row['produkt_id'];
        $produkt_name=$row['produkt_name'];
        $produkt_description=$row['produkt_description'];
        $produkt_image1=$row['produkt_image1'];
        $produkt_price=$row['produkt_price'];
        $liga_id=$row['liga_id'];
        $ekip_id=$row['ekip_id'];
        echo "<div class='col-md-4'>
              <div class='card'>

                <div class='imgBox'>
                  <img src='./admin_manage/produkt_image/$produkt_image1' alt='$produkt_name' class='mouse'>
                </div>

                <div class='contentBox'>
                  <h3>$produkt_name</h3>
                  <h2 class='price'>$produkt_price €</h2>
                  <a href='produkt_info.php?produkt_id=$produkt_id' class='buy'>View More</a>
                  

                </div>

              </div>
            </div>";
      }
    }
}
}


function getproduktbyliga(){
    global $con;
    if(isset($_GET['liga'])){
    $liga_id= $_GET['liga'];  
      $select_query="Select * from `produkt` where liga_id=$liga_id";
      $result_query=mysqli_query($con,$select_query);
      $num_of_rows=mysqli_num_rows($result_query);
      if($num_of_rows==0){
        echo "<h2 class='text-center text-danger'>Nuk ka stok per kete lige.";
      }      
      while($row=mysqli_fetch_assoc($result_query)){
        $produkt_id=$row['produkt_id'];
        $produkt_name=$row['produkt_name'];
        $produkt_description=$row['produkt_description'];
        $produkt_image1=$row['produkt_image1'];
        $produkt_price=$row['produkt_price'];
        $liga_id=$row['liga_id'];
        $ekip_id=$row['ekip_id'];
        echo "<div class='col-md-4'>
              <div class='card'>

                <div class='imgBox'>
                  <img src='./admin_manage/produkt_image/$produkt_image1' alt='$produkt_name' class='mouse'>
                </div>

                <div class='contentBox'>
                  <h3>$produkt_name</h3>
                  <h2 class='price'>$produkt_price €</h2>
                  <a href='produkt_info.php?produkt_id=$produkt_id' class='buy'>View More</a>
                  

                </div>

              </div>
            </div>";
      }
    }
}

function getproduktbyekip(){
    global $con;
    if(isset($_GET['ekip'])){
    $ekip_id= $_GET['ekip'];  
      $select_query="Select * from `produkt` where ekip_id=$ekip_id";
      $result_query=mysqli_query($con,$select_query);
      $num_of_rows=mysqli_num_rows($result_query);
      if($num_of_rows==0){
        echo "<h2 class='text-center text-danger'>Nuk ka stok per kete ekip.";
      }      
      while($row=mysqli_fetch_assoc($result_query)){
        $produkt_id=$row['produkt_id'];
        $produkt_name=$row['produkt_name'];
        $produkt_description=$row['produkt_description'];
        $produkt_image1=$row['produkt_image1'];
        $produkt_price=$row['produkt_price'];
        $liga_id=$row['liga_id'];
        $ekip_id=$row['ekip_id'];
        echo "<div class='col-md-4'>
              <div class='card'>

                <div class='imgBox'>
                  <img src='./admin_manage/produkt_image/$produkt_image1' alt='$produkt_name' class='mouse'>
                </div>

                <div class='contentBox'>
                  <h3>$produkt_name</h3>
                  <h2 class='price'>$produkt_price €</h2>
                  <a href='produkt_info.php?produkt_id=$produkt_id' class='buy'>View More</a>
                  

                </div>

              </div>
            </div>";
      }
    }
}



function getliga(){
  global $con;
  $select_liga = "SELECT * FROM `liga`";
  $result_liga = mysqli_query($con, $select_liga);

  while ($row_data = mysqli_fetch_assoc($result_liga)) {
      $liga_name = $row_data['liga_name'];
      $liga_id = $row_data['liga_id'];
      echo "
      <li class='nav-item' style='background-color: #000000;'>
          <a class='nav-link' href='index.php?liga=$liga_id' 
             style='color: white;' 
             onmouseover='this.style.color=\"#ffce00\"; this.style.backgroundColor=\"#333333\";' 
             onmouseout='this.style.color=\"white\"; this.style.backgroundColor=\"\";'>$liga_name</a>
      </li>";
  }
}

function getekip(){
  global $con;
  $select_ekip = "SELECT * FROM `ekip`";
  $result_ekip = mysqli_query($con, $select_ekip);

  while ($row_data = mysqli_fetch_assoc($result_ekip)) {
      $ekip_name = $row_data['ekip_name'];
      $ekip_id = $row_data['ekip_id'];
      echo "
      <li class='nav-item' style='background-color: #000000;'>
          <a class='nav-link' href='index.php?ekip=$ekip_id' 
             style='color: white;' 
             onmouseover='this.style.color=\"#ffce00\"; this.style.backgroundColor=\"#333333\";' 
             onmouseout='this.style.color=\"white\"; this.style.backgroundColor=\"\";'>$ekip_name</a>
      </li>";
  }
}



          
    function search_produkt(){
        global $con;
        if(isset($_GET['search_produkt'])){
          $search_produkt_value=$_GET['search_produkt'];
          $search_query="Select * from `produkt` where produkt_keywords like '%$search_produkt_value%'";
          $result_query=mysqli_query($con,$search_query);
          $num_of_rows=mysqli_num_rows($result_query);
          if($num_of_rows==0){
          echo "<h2 class='text-center text-danger'>Produkti qe kerkoni nuk ekziston.";
          } 
          while($row=mysqli_fetch_assoc($result_query)){
            $produkt_id=$row['produkt_id'];
            $produkt_name=$row['produkt_name'];
            $produkt_description=$row['produkt_description'];
            $produkt_image1=$row['produkt_image1'];
            $produkt_price=$row['produkt_price'];
            $liga_id=$row['liga_id'];
            $ekip_id=$row['ekip_id'];
            echo "<div class='col-md-4'>
            <div class='card' style='width: 18rem;'>
              <img src='./admin_manage/produkt_image/$produkt_image1' class='card-img-top' alt='$produkt_name'>
              <div class='card-body'>
                <h5 class='card-title'>$produkt_name</h5>
                <p class='card-text'>$produkt_description</p>
                <p class='card-text'>$produkt_price $</p>
            <a href='index.php?add_to_cart=$produkt_id' class='btn btn-info'>Add To Cart</a>
                <a href='#' class='btn btn-secondary'>View More</a>
              </div>
            </div>
          </div>";
          }
        }
      }

      
      
      
      function view_more() {
        global $con;
        if (isset($_GET['produkt_id'])) {
            $produkt_id = $_GET['produkt_id'];


            // Fetch product details
            $select_query = "SELECT * FROM produkt WHERE produkt_id = $produkt_id";
            $result_query = mysqli_query($con, $select_query);
            while ($row = mysqli_fetch_assoc($result_query)) {
                $produkt_name = $row['produkt_name'];
                $produkt_description = $row['produkt_description'];
                $produkt_image1 = $row['produkt_image1'];
                $produkt_image2 = $row['produkt_image2'];
                $produkt_image3 = $row['produkt_image3'];
                $produkt_price = $row['produkt_price'];
    
                // Start of HTML output
                echo "
                <div id='content-wrapper'>
                    <div class='column'>
                        <img id='featured' src='./admin_manage/produkt_image/$produkt_image1' alt='$produkt_name'>
                        <div id='thumbnails-wrapper'>
                            <img class='thumbnail thumbnail-active' src='./admin_manage/produkt_image/$produkt_image1' alt='$produkt_name Image 1'>
                            <img class='thumbnail' src='./admin_manage/produkt_image/$produkt_image2' alt='$produkt_name Image 2'>
                            <img class='thumbnail' src='./admin_manage/produkt_image/$produkt_image3' alt='$produkt_name Image 3'>
                        </div>
                    </div>
                    <div class='column'>
                        <h1>$produkt_name</h1>
                        <hr>
                        <h3>\$$produkt_price</h3>
                        <p>$produkt_description</p>
    
                        <!-- Size Selector -->
                        <div class='size-selector'>
                            <label for='size'>Select Size</label>
                            <div class='sizes' style='display: flex; justify-content: center; gap: 10px; margin-top: 10px;'>";
    
                // Define all sizes
                $all_sizes = ['S', 'M', 'L', 'XL', 'XXL'];
    
                // Ensure $produkt_id is defined and valid before the query
                $produkt_id = (int) $produkt_id;  // Casting to int to ensure it's numeric
    
                // Fetch stock information for sizes
                $size_query = "SELECT * FROM sizes WHERE produkt_id = $produkt_id";
                $size_result = mysqli_query($con, $size_query);
                $size_stock = array();  // Initialize the array
                $size_ids = array();    // Initialize the array
    
                if ($size_result) {
                    while ($size_row = mysqli_fetch_assoc($size_result)) {
                        // Check if 'size' and 'stock' keys exist in the result
                        if (isset($size_row['size']) && isset($size_row['stock'])) {
                            $size_stock[$size_row['size']] = (int) $size_row['stock'];  // Cast stock to integer
                            $size_ids[$size_row['size']] = (int) $size_row['size_id'];  // Cast size_id to integer
                        }
                    }
                } else {
                    // Handle query failure
                    echo "Error fetching size data: " . mysqli_error($con);
                }



    
                // Render size buttons dynamically
                foreach ($all_sizes as $size) {
                    $stock = isset($size_stock[$size]) ? $size_stock[$size] : 0;
                    $size_id = isset($size_ids[$size]) ? $size_ids[$size] : null;
                    $disabled = ($stock <= 0) ? 'disabled' : '';
                    $class = ($stock <= 0) ? 'out-of-stock' : 'in-stock';
    
                    // Render the size button
                    echo "<button class='size-btn $class' data-size='$size' data-size-id='$size_id' $disabled>$size</button>";
                }

               
    
                echo "
                            </div>
                        </div>
    
                        <!-- Add to Cart Button -->
                        <a class='btn add-to-cart-btn' id='addToCartBtn' href='' data-produkt-id='$produkt_id'>Add to Cart</a>
                        <a href='review.php?produkt_id=$produkt_id' class='btn btn-primary mb-2'>Review</a>

                        </div>
                </div>

                


    
                <!-- Inline JavaScript -->
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const sizeButtons = document.querySelectorAll('.size-btn');
                        const addToCartButton = document.getElementById('addToCartBtn');
                        let selectedSize = null;
                        let selectedSizeId = null;
    
                        // Disable 'Add to Cart' button initially
                        addToCartButton.disabled = true;
                        addToCartButton.style.opacity = '0.5';
                        addToCartButton.style.cursor = 'not-allowed';
    
                        sizeButtons.forEach(button => {
                            button.addEventListener('click', function () {
                                if (this.classList.contains('active')) {
                                    this.classList.remove('active');
                                    selectedSize = null;
                                    selectedSizeId = null;
                                } else {
                                    sizeButtons.forEach(btn => btn.classList.remove('active'));
                                    this.classList.add('active');
                                    selectedSize = this.getAttribute('data-size');
                                    selectedSizeId = this.getAttribute('data-size-id');
                                }
    
                                if (selectedSize) {
                                    addToCartButton.disabled = false;
                                    addToCartButton.style.opacity = '1';
                                    addToCartButton.style.cursor = 'pointer';
                                } else {
                                    addToCartButton.disabled = true;
                                    addToCartButton.style.opacity = '0.5';
                                    addToCartButton.style.cursor = 'not-allowed';
                                }
                            });
                        });
    
                        // Add to Cart Logic
                        addToCartButton.addEventListener('click', function (event) {
                            if (this.disabled) {
        // Prevent any action if the button is disabled
        event.preventDefault();
        return;
    }
    
                            if (selectedSize && selectedSizeId) {
                                const produktId = this.getAttribute('data-produkt-id');
    
                                const formData = new FormData();
                                formData.append('produkt_id', produktId);
                                formData.append('size', selectedSize);
                                formData.append('size_id', selectedSizeId);
    
                                fetch('add_to_cart.php', {
                                    method: 'POST',
                                    body: formData
                                })
                                .then(response => response.json())
                                .then(data => {
                                    alert(data.message);
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    alert('Failed to add item to cart.');
                                });
                            }
                        });
    
                        // Thumbnail Logic
                        const thumbnails = document.querySelectorAll('.thumbnail');
                        const featuredImage = document.getElementById('featured');
    
                        thumbnails.forEach(thumbnail => {
                            thumbnail.addEventListener('mouseover', function () {
                                thumbnails.forEach(thumb => thumb.classList.remove('thumbnail-active'));
                                this.classList.add('thumbnail-active');
                                featuredImage.src = this.src;
                            });
                        });
                    });
                </script>";

                echo "<div class='testimonials-container'>
                <h2>Reviews</h2>
                <p class='description'>
                   We are committed to delivering products that make a difference. But don’t just take our word for it — read what our customers have to say! Their stories reflect our dedication to quality, service, and innovation.


                </p>";

            // Fetch testimonials for the product
            $testimonial_query = "SELECT * FROM testimonials WHERE produkt_id = $produkt_id ORDER BY id DESC";
            $testimonial_result = mysqli_query($con, $testimonial_query);

            if (mysqli_num_rows($testimonial_result) > 0) {
                echo "<div class='testimonials'>";
                while ($testimonial = mysqli_fetch_assoc($testimonial_result)) {
                  
                    $image_url = $testimonial['image_url'] ?: 'https://via.placeholder.com/100';
                    $name = htmlspecialchars($testimonial['name']);
                    $quote = htmlspecialchars($testimonial['testimonial']);

                    echo "
                    <div class='card'>
                        <div class='image-container'>
                            <img src='$image_url' alt='$name'>
                        </div>
                        <h3>$name</h3>
                        <p class='quote'>\"$quote\"</p>
                    </div>";
                }
                echo "</div>";
            } else {
                echo "<p>No testimonials available for this product yet.</p>";
            }

            echo "</div>"; 


            }
        }
    }
    
    
      
    


function getCartProductNumber() {
  global $con;

  
  if (session_status() === PHP_SESSION_NONE) {
      session_start();
  }

  // Check if user is logged in
  if (!isset($_SESSION['id'])) {
      return 0; // Return 0 if not logged in
  }

  $user_id = $_SESSION['id'];
  $query = "SELECT * FROM cart WHERE user_id = '$user_id'";
  $result = mysqli_query($con, $query);

  if ($result) {
      return mysqli_num_rows($result);   
  } else {
      return 0; 
  }
}
        


