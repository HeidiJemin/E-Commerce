<?php

require_once('../includes/connect.php');

function getprodukt() {
  global $con;
  if (!isset($_GET['liga']) && !isset($_GET['ekip'])) {
      $select_query = "SELECT * FROM produkt ORDER BY RAND() LIMIT 0,9";
      $result_query = mysqli_query($con, $select_query);

      if (mysqli_num_rows($result_query) > 0) {
          while ($row = mysqli_fetch_assoc($result_query)) {
              $produkt_id = $row['produkt_id'];
              $produkt_name = $row['produkt_name'];
              $produkt_description = $row['produkt_description'];
              $produkt_image1 = $row['produkt_image1'];
              $produkt_price = $row['produkt_price'];

              
              $is_favourited = false;
              if (isset($_SESSION['id'])) {
                  $user_id = $_SESSION['id'];
                  $fav_query = "SELECT * FROM favourites WHERE produkt_id = $produkt_id AND user_id = $user_id";
                  $fav_result = mysqli_query($con, $fav_query);
                  $is_favourited = mysqli_num_rows($fav_result) > 0;
              }

              $favourite_text = $is_favourited ? 'Remove from Favourites' : 'Add to Favourites';
              $btn_class = $is_favourited ? 'favourited' : '';

              echo "<div class='col-md-4'>
                      <div class='card'>
                          <div class='imgBox'>
                              <img src='../admin_manage/produkt_image/$produkt_image1' alt='$produkt_name' class='mouse'>
                          </div>
                          <div class='contentBox'>
                              <h3>$produkt_name</h3>
                              <h2 class='price'>$produkt_price €</h2>
                              <a href='produkt_info.php?produkt_id=$produkt_id' class='buy'>View More</a>";

              
              if (isset($_SESSION['id']) && isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1) {
                  $btn_class = $is_favourited ? 'favourited' : '';
                  $btn_text = $is_favourited ? 'Remove from Favourites' : 'Add to Favourites';
                  echo "<button class='favourite-btn $btn_class' data-produkt-id='$produkt_id'>
                            $btn_text
                        </button>";
              }

              echo "    </div>
                      </div>
                    </div>";
          }
      } else {
          // no products are found
          echo "<div class='text-center' style='width: 100%; padding: 20px;'>
                  <h4>No products available at the moment.</h4>
                </div>";
      }
  }
  
}

function get_all_favourites() {
    global $con;

    if (isset($_SESSION['id'])) {
        $user_id = $_SESSION['id'];

        
        $query = "SELECT p.* FROM favourites f 
                  INNER JOIN produkt p ON f.produkt_id = p.produkt_id 
                  WHERE f.user_id = $user_id";

        $result = mysqli_query($con, $query);

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $produkt_id = $row['produkt_id'];
                $produkt_name = $row['produkt_name'];
                $produkt_description = $row['produkt_description'];
                $produkt_image1 = $row['produkt_image1'];
                $produkt_price = $row['produkt_price'];

                echo "<div class='col-md-4'>
                        <div class='card' data-produkt-id='$produkt_id'>
                            <div class='imgBox'>
                                <img src='../admin_manage/produkt_image/$produkt_image1' alt='$produkt_name' class='mouse'>
                            </div>
                            <div class='contentBox'>
                                <h3>$produkt_name</h3>
                                <h2 class='price'>$produkt_price €</h2>
                                <a href='produkt_info.php?produkt_id=$produkt_id' class='buy'>View More</a>
                                <button class='favourite-btn favourited' data-produkt-id='$produkt_id'>
                                    Remove from Favourites
                                </button>
                            </div>
                        </div>
                      </div>";
            }
        } else {
            echo "<div class='text-center ' style='width: 100%; padding: 20px;'>
                    <h4>No favourite products yet.</h4>
                  </div>";
        }
    } else {
        echo "<div class='text-center' style='width: 100%; padding: 20px;'>
                <h4>Please log in to see your favourites.</h4>
              </div>";
    }
}








function get_all_produkt(){
  global $con;
  if (!isset($_GET['liga']) && !isset($_GET['ekip'])) {
    $select_query = "SELECT * FROM produkt ORDER BY RAND()";
    $result_query = mysqli_query($con, $select_query);

    if (mysqli_num_rows($result_query) > 0) {
        while ($row = mysqli_fetch_assoc($result_query)) {
            $produkt_id = $row['produkt_id'];
            $produkt_name = $row['produkt_name'];
            $produkt_description = $row['produkt_description'];
            $produkt_image1 = $row['produkt_image1'];
            $produkt_price = $row['produkt_price'];

            
            $is_favourited = false;
            if (isset($_SESSION['id'])) {
                $user_id = $_SESSION['id'];
                $fav_query = "SELECT * FROM favourites WHERE produkt_id = $produkt_id AND user_id = $user_id";
                $fav_result = mysqli_query($con, $fav_query);
                $is_favourited = mysqli_num_rows($fav_result) > 0;
            }

            $favourite_text = $is_favourited ? 'Remove from Favourites' : 'Add to Favourites';
            $btn_class = $is_favourited ? 'favourited' : '';

            echo "<div class='col-md-4'>
                    <div class='card'>
                        <div class='imgBox'>
                            <img src='../admin_manage/produkt_image/$produkt_image1' alt='$produkt_name' class='mouse'>
                        </div>
                        <div class='contentBox'>
                            <h3>$produkt_name</h3>
                            <h2 class='price'>$produkt_price €</h2>
                            <a href='produkt_info.php?produkt_id=$produkt_id' class='buy'>View More</a>";

            
            if (isset($_SESSION['id']) && isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1) {
                $btn_class = $is_favourited ? 'favourited' : '';
                $btn_text = $is_favourited ? 'Remove from Favourites' : 'Add to Favourites';
                echo "<button class='favourite-btn $btn_class' data-produkt-id='$produkt_id'>
                          $btn_text
                      </button>";
            }

            echo "    </div>
                    </div>
                  </div>";
        }
    } else {
        //  no products are found
        echo "<div class='text-center' style='width: 100%; padding: 20px;'>
                <h4>No products available at the moment.</h4>
              </div>";
    }
}
}


function getproduktbyliga() {
  global $con;

  if (isset($_GET['liga'])) {
    
$adminPath = '../admin_manage/index.php';

// if user is logged in
if (isset($_SESSION['id'])) {
    // not user
    if ($_SESSION['role_id'] != 1) {
        // admin page if role_id is not 1
        header("Location: $adminPath");
        exit();
    }
} else {
    
    header("Location: login.php");
    exit();
}

      $liga_id = $_GET['liga'];
      $select_query = "SELECT * FROM `produkt` WHERE liga_id = $liga_id";
      $result_query = mysqli_query($con, $select_query);
      $num_of_rows = mysqli_num_rows($result_query);

      if ($num_of_rows == 0) {
          echo "<h2 class='text-center text-danger'>Nuk ka stok për këtë ligë.</h2>";
      } else {
          while ($row = mysqli_fetch_assoc($result_query)) {
              $produkt_id = $row['produkt_id'];
              $produkt_name = $row['produkt_name'];
              $produkt_description = $row['produkt_description'];
              $produkt_image1 = $row['produkt_image1'];
              $produkt_price = $row['produkt_price'];

              
              $is_favourited = false;
              if (isset($_SESSION['id'])) {
                  $user_id = $_SESSION['id'];
                  $fav_query = "SELECT * FROM favourites WHERE produkt_id = $produkt_id AND user_id = $user_id";
                  $fav_result = mysqli_query($con, $fav_query);
                  $is_favourited = mysqli_num_rows($fav_result) > 0;
              }

              $btn_class = $is_favourited ? 'favourited' : '';
              $btn_text = $is_favourited ? 'Remove from Favourites' : 'Add to Favourites';

              echo "<div class='col-md-4'>
                      <div class='card'>
                          <div class='imgBox'>
                              <img src='../admin_manage/produkt_image/$produkt_image1' alt='$produkt_name' class='mouse'>
                          </div>
                          <div class='contentBox'>
                              <h3>$produkt_name</h3>
                              <h2 class='price'>$produkt_price €</h2>
                              <a href='produkt_info.php?produkt_id=$produkt_id' class='buy'>View More</a>";

              // Add to Favorites button if the user is logged in
              if (isset($_SESSION['id']) && isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1) {
                  echo "<button class='favourite-btn $btn_class' data-produkt-id='$produkt_id'>
                          $btn_text
                        </button>";
              }

              echo "      </div>
                      </div>
                    </div>";
          }
      }
  }
}


function getproduktbyekip() {
  global $con;



  if (isset($_GET['ekip'])) {

    $adminPath = '../admin_manage/index.php';


if (isset($_SESSION['id'])) {
    // not user
    if ($_SESSION['role_id'] != 1) {
        // admin page if role_id is not 1
        header("Location: $adminPath");
        exit();
    }
} else {
    
    header("Location: login.php");
    exit();
}
    
      $ekip_id = $_GET['ekip'];
      $select_query = "SELECT * FROM `produkt` WHERE ekip_id = $ekip_id";
      $result_query = mysqli_query($con, $select_query);
      $num_of_rows = mysqli_num_rows($result_query);

      if ($num_of_rows == 0) {
          echo "<h2 class='text-center text-danger'>Nuk ka stok për këtë ekip.</h2>";
      } else {
          while ($row = mysqli_fetch_assoc($result_query)) {
              $produkt_id = $row['produkt_id'];
              $produkt_name = $row['produkt_name'];
              $produkt_description = $row['produkt_description'];
              $produkt_image1 = $row['produkt_image1'];
              $produkt_price = $row['produkt_price'];

              
              $is_favourited = false;
              if (isset($_SESSION['id'])) {
                  $user_id = $_SESSION['id'];
                  $fav_query = "SELECT * FROM favourites WHERE produkt_id = $produkt_id AND user_id = $user_id";
                  $fav_result = mysqli_query($con, $fav_query);
                  $is_favourited = mysqli_num_rows($fav_result) > 0;
              }

              $btn_class = $is_favourited ? 'favourited' : '';
              $btn_text = $is_favourited ? 'Remove from Favourites' : 'Add to Favourites';

              echo "<div class='col-md-4'>
                      <div class='card'>
                          <div class='imgBox'>
                              <img src='../admin_manage/produkt_image/$produkt_image1' alt='$produkt_name' class='mouse'>
                          </div>
                          <div class='contentBox'>
                              <h3>$produkt_name</h3>
                              <h2 class='price'>$produkt_price €</h2>
                              <a href='produkt_info.php?produkt_id=$produkt_id' class='buy'>View More</a>";

              
              if (isset($_SESSION['id']) && isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1) {
                  echo "<button class='favourite-btn $btn_class' data-produkt-id='$produkt_id'>
                          $btn_text
                        </button>";
              }

              echo "      </div>
                      </div>
                    </div>";
          }
      }
  }
}




function getliga(){
  global $con;
  $select_liga = "SELECT * FROM `liga` ORDER BY RAND() LIMIT 10";
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
  $select_ekip = "SELECT * FROM `ekip` ORDER BY RAND() LIMIT 10";
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

            
            $is_favourited = false;
            if (isset($_SESSION['id'])) {
                $user_id = $_SESSION['id'];
                $fav_query = "SELECT * FROM favourites WHERE produkt_id = $produkt_id AND user_id = $user_id";
                $fav_result = mysqli_query($con, $fav_query);
                $is_favourited = mysqli_num_rows($fav_result) > 0;
            }

            $btn_class = $is_favourited ? 'favourited' : '';
            $btn_text = $is_favourited ? 'Remove from Favourites' : 'Add to Favourites';

            echo "<div class='col-md-4'>
                    <div class='card'>
                        <div class='imgBox'>
                            <img src='../admin_manage/produkt_image/$produkt_image1' alt='$produkt_name' class='mouse'>
                        </div>
                        <div class='contentBox'>
                            <h3>$produkt_name</h3>
                            <h2 class='price'>$produkt_price €</h2>
                            <a href='produkt_info.php?produkt_id=$produkt_id' class='buy'>View More</a>";

            // Add to Favourites button if the user is logged in
            if (isset($_SESSION['id']) && isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1) {
                $btn_class = $is_favourited ? 'favourited' : '';
                $btn_text = $is_favourited ? 'Remove from Favourites' : 'Add to Favourites';
                echo "<button class='favourite-btn $btn_class' data-produkt-id='$produkt_id'>
                          $btn_text
                      </button>";
            }

            echo "    </div>
                    </div>
                  </div>";
        }
    }
}


      
      
      
      function view_more() {
        global $con;
        if (isset($_GET['produkt_id'])) {
            $produkt_id = $_GET['produkt_id'];


            
            $select_query = "SELECT * FROM produkt WHERE produkt_id = $produkt_id";
            $result_query = mysqli_query($con, $select_query);
            // Check if the product exists
        if (!$result_query || mysqli_num_rows($result_query) === 0) {
            echo "<h2 class='text-center text-danger'>Ky produkt nuk ekziston.</h2>";
            return; 
        }
            while ($row = mysqli_fetch_assoc($result_query)) {
                $produkt_name = $row['produkt_name'];
                $produkt_description = $row['produkt_description'];
                $produkt_image1 = $row['produkt_image1'];
                $produkt_image2 = $row['produkt_image2'];
                $produkt_image3 = $row['produkt_image3'];
                $produkt_price = $row['produkt_price'];
    
                
                echo "
                <div id='content-wrapper'>
                    <div class='column'>
                        <img id='featured' src='../admin_manage/produkt_image/$produkt_image1' alt='$produkt_name'>
                        <div id='thumbnails-wrapper'>
                            <img class='thumbnail thumbnail-active' src='../admin_manage/produkt_image/$produkt_image1' alt='$produkt_name Image 1'>
                            <img class='thumbnail' src='../admin_manage/produkt_image/$produkt_image2' alt='$produkt_name Image 2'>
                            <img class='thumbnail' src='../admin_manage/produkt_image/$produkt_image3' alt='$produkt_name Image 3'>
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
    
                // all sizes
                $all_sizes = ['S', 'M', 'L', 'XL', 'XXL'];
    
                
                $produkt_id = (int) $produkt_id;  
    
                // Fetch stock information for sizes
                $size_query = "SELECT * FROM sizes WHERE produkt_id = $produkt_id";
                $size_result = mysqli_query($con, $size_query);
                $size_stock = array();  
                $size_ids = array();
    
                if ($size_result) {
                    while ($size_row = mysqli_fetch_assoc($size_result)) {
                        
                        if (isset($size_row['size']) && isset($size_row['stock'])) {
                            $size_stock[$size_row['size']] = (int) $size_row['stock'];  
                            $size_ids[$size_row['size']] = (int) $size_row['size_id'];  
                        }
                    }
                } else {
                    
                    echo "Error fetching size data: " . mysqli_error($con);
                }



    
                // size buttons 
                foreach ($all_sizes as $size) {
                    $stock = isset($size_stock[$size]) ? $size_stock[$size] : 0;
                    $size_id = isset($size_ids[$size]) ? $size_ids[$size] : null;
                    $disabled = ($stock <= 0) ? 'disabled' : '';
                    $class = ($stock <= 0) ? 'out-of-stock' : 'in-stock';
    
                    // size button
                    echo "<button class='size-btn $class' data-size='$size' data-size-id='$size_id' $disabled>$size</button>";
                }

               
    
                echo "
                            </div>
                        </div>
    
                        <!-- Add to Cart Button -->
                        <a class='btn add-to-cart-btn' id='addToCartBtn' href='' data-produkt-id='$produkt_id'>Add to Cart</a>";

                        if (isset($_SESSION['id']) && $_SESSION['role_id'] == 1) {
                            echo "
                            <div style='margin-top: 10px; margin-bottom: 10px;'>
                                <a href='review.php?produkt_id=$produkt_id' class='btn btn-primary' style='background-color: yellow; color: black;'>Leave a Review</a>
                            </div>";
                        }

                       echo " </div>
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
                        addToCartButton.style.pointerEvents = 'none';
    
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
                                    addToCartButton.style.pointerEvents = ''; // Re-enable pointer interactions
                                } else {
                                    addToCartButton.disabled = true;
                                    addToCartButton.style.opacity = '0.5';
                                    addToCartButton.style.cursor = 'not-allowed';
                                    addToCartButton.style.pointerEvents = 'none'; // Re-enable pointer interactions
                                }
                            });
                        });
    
                        // Add to Cart Logic
addToCartButton.addEventListener('click', function (event) {
    event.preventDefault();
    if (this.disabled) {
        return;
    }

    if (selectedSize && selectedSizeId) {
        const produktId = this.getAttribute('data-produkt-id');

        const formData = new FormData();
        formData.append('produkt_id', produktId);
        formData.append('size', selectedSize);
        formData.append('size_id', selectedSizeId);

        //  add the item to the cart
        fetch('./controllers/add_to_cart.php', {
        method: 'POST',
        body: formData,
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                
                toastr.success(data.message);

                
                updateCartCount();
            } else {
                
                toastr.error(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            toastr.error('An error occurred while trying to add the product to the cart.');
        });
    }
});

// update cart count 
function updateCartCount() {
    fetch('./controllers/get_cart_count.php')
        .then(response => response.text())
        .then(cartCount => {
            
            document.getElementById('cart-count').textContent = cartCount;
        })
        .catch(error => {
            console.error('Error fetching cart count:', error);
        });
}

    
                        // Thumbnail
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

            
            // testimonials for the product
            $testimonial_query = "SELECT name, foto, testimonial,rating,satisfaction ,experience_date 
            FROM testimonials 
            LEFT JOIN users ON users.user_id = testimonials.user_id 
            WHERE produkt_id = $produkt_id 
            ORDER BY id DESC";

            $testimonial_result = mysqli_query($con, $testimonial_query);

            if (mysqli_num_rows($testimonial_result) > 0) {
                echo "<div class='testimonials'>";
                while ($testimonial = mysqli_fetch_assoc($testimonial_result)) {
                  
                    // Image and text data
                    $image_url = $testimonial['foto'] ?: 'https://st3.depositphotos.com/15648834/17930/v/600/depositphotos_179308454-stock-illustration-unknown-person-silhouette-glasses-profile.jpg';
                    $name = htmlspecialchars($testimonial['name']);
                    $quote = htmlspecialchars($testimonial['testimonial']);
                    $rating = (int)$testimonial['rating']; 
                    $satisfaction = isset($testimonial['satisfaction']) ? (int)$testimonial['satisfaction'] : 0;
                    $experience_date = !empty($testimonial['experience_date']) ? date("F j, Y", strtotime($testimonial['experience_date'])) : "Date not available";
                    
                    $stars = str_repeat('⭐', $rating);
            
                    
                    $emoji = '';
                    switch ($satisfaction) {
                        case 1:
                            $emoji = '😢'; // Very Unsatisfied
                            break;
                        case 2:
                            $emoji = '🙁'; // Unsatisfied
                            break;
                        case 3:
                            $emoji = '😐'; // Neutral
                            break;
                        case 4:
                            $emoji = '🙂'; // Satisfied
                            break;
                        case 5:
                            $emoji = '😊'; // Very Satisfied
                            break;
                        default:
                            $emoji = '🤔'; // Unknown satisfaction level
                            break;
                    }
            
                    // Output the testimonial card
                    echo "
                    <div class='card'>
                        <div class='image-container'>
                            <img src='" . ($testimonial['foto'] ? "../uploads/{$testimonial['foto']}" : "https://st3.depositphotos.com/15648834/17930/v/600/depositphotos_179308454-stock-illustration-unknown-person-silhouette-glasses-profile.jpg") . "' alt='{$testimonial['name']}'>

                        </div>
                        <h3>$name</h3>
                        <p class='quote'>\"$quote\"</p>
                        <p class='stars'>$stars</p>
                        <p class='emoji'>$emoji</p>
                        <p class='date'>Experience Date: $experience_date</p>

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
  if (!isset($_SESSION['id']) || $_SESSION['role_id']!=1) {
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



?>


