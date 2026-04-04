<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require_once ABSOLUTE_PATH . 'vendor/autoload.php';

    function get($key) {
        if(isset($_GET[$key])) {
            return $_GET[$key];
        }
        return null;
    }

    function post($key) {
        if(isset($_POST[$key])) {
            return $_POST[$key];
        }
        return null;
    }

    function hasFlash($key){
        return isset($_SESSION[$key]) && $_SESSION[$key];
    }

    function setFlash($key,$value){
        $_SESSION[$key] = $value;
    }

    function getFlashData($key){
        if(!hasFlash($key)){
            return null;
        }
        $data = $_SESSION[$key];
        unset($_SESSION[$key]);
        return $data;
    }


    logFile($data = null);

    function logFile($data = null){
        $open = fopen(LOG_FILE, "a");
        
        if($open){
            $logData = $data;
            
            fwrite($open, $logData);
            
            fclose($open);
        } else {
            error_log("Failed to open log file: " . LOG_FILE);
        }
    }

    function logData($type){
        $data = file("../data/log.txt");

        $array = [];

        foreach($data as $d){
            $data = explode("\t", $d);

            $one = new stdClass();
            $one->type = $data[0];

            if($one->type == '[PAGE_VISIT]' && $type == '[PAGE_VISIT]'){
                $one->page = $data[1];
                $one->ip = $data[2];
                $one->time = $data[3];

                $array[] = $one;
            }

            if($one->type == '[USER_LOGIN]' && $type == '[USER_LOGIN]'){
                $one->id = $data[1];
                $one->ip = $data[2];
                $one->time = $data[3];

                $array[] = $one;
            }
        }

        return $array;
    }

    function getLastRow($data){
        return end($data);
    }

    function getAllFromTable($table){
        global $conn;
        $query = "SELECT * FROM $table";
        $result = $conn->query($query)->fetchAll();
        return $result;
    }

    function getProducts(){
        global $conn;
        $query = "SELECT p.id_product AS id, p.name, p.description, p.cover_photo, d.value AS discount_value 
        FROM products p 
        LEFT JOIN product_discount pd ON p.id_product = pd.id_product 
        LEFT JOIN discount d ON pd.id_discount = d.id_discount 
        ORDER BY p.id_product ASC";
        $result = $conn->query($query)->fetchAll();
        return $result;
    }
    
    function getProductPrice($productId) {
        global $conn;

        $oldPriceQuery = "
            SELECT value 
            FROM price 
            WHERE id_product = :productId
            ORDER BY date_start DESC, id_price DESC
            LIMIT 1
        ";
        $oldPriceStmt = $conn->prepare($oldPriceQuery);
        $oldPriceStmt->bindParam(':productId', $productId);
        $oldPriceStmt->execute();
        $oldPriceRow = $oldPriceStmt->fetch(PDO::FETCH_ASSOC);

        $oldPrice = $oldPriceRow ? (float)$oldPriceRow['value'] : 0;

        $discountQuery = "
            SELECT d.value 
            FROM discount d 
            INNER JOIN product_discount pd ON d.id_discount = pd.id_discount 
            WHERE pd.id_product = :productId
        ";
        $discountStmt = $conn->prepare($discountQuery);
        $discountStmt->bindParam(':productId', $productId);
        $discountStmt->execute();
        $discountValue = (float)($discountStmt->fetch(PDO::FETCH_ASSOC)['value'] ?? 0);

        $newPrice = $oldPrice * (1 - $discountValue / 100);

        return [
            'oldPrice' => $oldPrice,
            'newPrice' => $newPrice
        ];
    }



    function getProductDiscount($productId) {
        global $conn;
        $query = "SELECT d.value FROM discount d LEFT JOIN product_discount pd ON d.id_discount = pd.id_discount WHERE pd.id_product = :productId";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':productId', $productId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result != null ? $result['value'] : 0; 
    }

    function getProductBasicDetails($productId) {
        global $conn;
        $query = "SELECT id_product AS id, name, is_active, description, cover_photo AS cover FROM products WHERE id_product = :productId";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':productId', $productId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function getProductImages($productId) {
        global $conn;
        $query = "SELECT picture_path FROM pictures WHERE id_product = :productId";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':productId', $productId);
        $stmt->execute();
        $images = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return $images;
    }
    
    function getProductColors($productId) {
        global $conn;
        $query = "SELECT id_color, name FROM color WHERE id_color IN (SELECT id_color FROM product_color WHERE id_product = :productId)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':productId', $productId);
        $stmt->execute();
        $colors = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $colors;
    }

    function getProductCategoryName($productId){
        global $conn;
        $query = "SELECT c.name FROM categories c INNER JOIN products p ON c.id_categories = p.id_categories WHERE p.id_product = :productId";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':productId', $productId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_COLUMN);
    }

    function getProductSizes($productId) {
        global $conn;
        $query = "SELECT id_size, name FROM size WHERE id_size IN (SELECT id_size FROM product_size WHERE id_product = :productId)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':productId', $productId);
        $stmt->execute();
        $sizes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $sizes;
    }
    

    function getFilteredSortedProducts($filters, $sort, $limit, $offset) {
        global $conn;
        
        $query = "
            SELECT DISTINCT 
                p.id_product AS id, 
                p.name,
                p.cover_photo, 
                d.value AS discount_value,
                d.date_start,
                d.date_finish,
                pr.value AS oldPrice,
                COALESCE(pr.value * (1 - d.value / 100), pr.value) AS price
            FROM products p
            LEFT JOIN product_discount pd ON p.id_product = pd.id_product
            LEFT JOIN discount d ON pd.id_discount = d.id_discount
            LEFT JOIN (
                SELECT id_product, value
                FROM price
                WHERE date_finish IS NULL
            ) pr ON p.id_product = pr.id_product

            LEFT JOIN product_size ps ON ps.id_product = p.id_product
            LEFT JOIN size s ON ps.id_size = s.id_size
            LEFT JOIN product_color pc ON p.id_product = pc.id_product
            LEFT JOIN color c ON pc.id_color = c.id_color
            WHERE 1=1
            AND p.is_active = 1";
        
        $params = [];
        
        if (!empty($filters['categories'])) {
            $placeholders = implode(',', array_fill(0, count($filters['categories']), '?'));
            $query .= " AND p.id_categories IN ($placeholders)";
            $params = array_merge($params, $filters['categories']);
        }
        
        if (!empty($filters['gender'])) {
            $query .= " AND p.id_gender = ?";
            $params[] = $filters['gender'];
        }
        
        
        if (!empty($filters['sizes'])) {
            $placeholders = implode(',', array_fill(0, count($filters['sizes']), '?'));
            $query .= " AND s.id_size IN ($placeholders)";
            $params = array_merge($params, $filters['sizes']);
        }
        
        if (!empty($filters['colors'])) {
            $placeholders = implode(',', array_fill(0, count($filters['colors']), '?'));
            $query .= " AND c.id_color IN ($placeholders)";
            $params = array_merge($params, $filters['colors']);
        }

        if (!empty($filters['priceMin']) && !empty($filters['priceMax'])) {
            $query .= " AND COALESCE(pr.value * (1 - d.value / 100), pr.value) BETWEEN ? AND ?";
            $params[] = $filters['priceMin'];
            $params[] = $filters['priceMax'];
        }

        
        switch ($sort) {
            case 'price_asc':
                $query .= " ORDER BY price ASC";
                break;
            case 'price_desc':
                $query .= " ORDER BY price DESC";
                break;
            case 'name_asc':
                $query .= " ORDER BY p.name ASC";
                break;
            case 'name_desc':
                $query .= " ORDER BY p.name DESC";
                break;
            case 'rating':
                $query .= " ORDER BY c.rating DESC";
                break;
            case 'discount':
                $query .= " ORDER BY d.value DESC";
                break;
            default:
                $query .= " ORDER BY p.id_product ASC";
        }
        
        $query .= " LIMIT ? OFFSET ?";
        
        $params[] = (int) $limit;
        $params[] = (int) $offset;
    
        $stmt = $conn->prepare($query);
        
        foreach ($params as $index => $param) {
            $stmt->bindValue($index + 1, $param, PDO::PARAM_INT);

        }
        
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    function getFilteredSortedProductCount($filters, $sort) {
        global $conn;
    
        $query = "
            SELECT COUNT(DISTINCT p.id_product) AS total
            FROM products p
            LEFT JOIN product_discount pd ON p.id_product = pd.id_product
            LEFT JOIN discount d ON pd.id_discount = d.id_discount
            LEFT JOIN price pr ON p.id_product = pr.id_product
            LEFT JOIN product_size ps ON ps.id_product = p.id_product
            LEFT JOIN size s ON ps.id_size = s.id_size
            LEFT JOIN product_color pc ON p.id_product = pc.id_product
            LEFT JOIN color c ON pc.id_color = c.id_color
            WHERE 1=1
            AND p.is_active = 1";
        
        $params = [];
        
        if (!empty($filters['categories'])) {
            $placeholders = implode(',', array_fill(0, count($filters['categories']), '?'));
            $query .= " AND p.id_categories IN ($placeholders)";
            $params = array_merge($params, $filters['categories']);
        }
    
        if (!empty($filters['gender'])) {
            $query .= " AND p.id_gender = ?";
            $params[] = $filters['gender'];
        }
        
    
        if (!empty($filters['sizes'])) {
            $placeholders = implode(',', array_fill(0, count($filters['sizes']), '?'));
            $query .= " AND s.id_size IN ($placeholders)";
            $params = array_merge($params, $filters['sizes']);
        }
    
        if (!empty($filters['colors'])) {
            $placeholders = implode(',', array_fill(0, count($filters['colors']), '?'));
            $query .= " AND c.id_color IN ($placeholders)";
            $params = array_merge($params, $filters['colors']);
        }
    
        if (!empty($filters['priceMin']) && !empty($filters['priceMax'])) {
            $query .= " AND COALESCE(pr.value * (1 - d.value / 100), pr.value) BETWEEN ? AND ?";
            $params[] = $filters['priceMin'];
            $params[] = $filters['priceMax'];
        }


        $stmt = $conn->prepare($query);
        $stmt->execute($params);
    
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    function getRegex($field) {
        $regexPatterns = [
            'name' => "/^[A-Z][a-zA-Z]{2,49}$/",
            'productName' => "/^([A-Z][a-z]+)(\s[A-Z][a-z]+)*$/",
            'email' => "/^[a-z]{5}[a-z0-9._,]*@[a-z]+\.[a-z]{2,}$/",
            'subject' => "/^[a-zA-Z0-9\s]{3,100}$/",
            'message' => "/^.{10,500}$/",
            'description' => "/^.{20,300}$/",
            'review' => "/^.{10,50}$/",
            'password' => "/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/",
            'streetName' => "/^([A-Z][a-z]{0,11})(\s[A-Z][a-z]{0,11})*$/",
            'streetNumber' => "/^[0-9]{1,5}[a-z]?$/",
            'phoneNumber' => "/^\d{8,10}$/",
            'quantity' => "/^(0|[1-9][0-9]*)$/"
        ];

        return $regexPatterns[$field] ?? null;
    }

    function messageText($field) {
        $messages = [
            'name' => "Name must start with an uppercase letter, contain only letters, and be between 3 and 50 characters long.",
            'productNameCheck' => "Product name must start with a capital letter and each word should start with a capital letter. Only letters and spaces are allowed.",
            'firstName' => "First name must start with an uppercase letter, contain only letters, and be between 3 and 50 characters long.",
            'lastName' => "Last name must start with an uppercase letter, contain only letters, and be between 3 and 50 characters long.",
            'email' => "Email must start with 5 lowercase letters, followed by lowercase letters, numbers, or symbols (. _ or ,). The domain name must only contain lowercase letters, and the top-level domain must be at least 2 lowercase letters.",
            'subject' => "Subject must be between 3 and 100 characters, and contain letters, numbers, or spaces.",
            'message' => "Message must be between 10 and 500 characters.",
            'description' => "Length must be between 20 and 300 characters.",
            'password' => "Password must be at least 8 characters long, include an uppercase letter, a lowercase letter, a number, and a special character (e.g., @, $, !, %, *, ?, &).",
            'contactSuccess' => "Message submitted successfully.",
            'registerSuccess' => "Please check your email to verify your account.",
            'tokenExpired' => 'Your verification link has expired. A new verification email has been sent.',
            'unverifiedAccountNewToken' => "Account not verified. A new verification email has been sent.",
            'formSubmitError' => "Error while submitting the form, please check the data again.",
            'userExists' => "User already registered. Please log in.",
            'subscriptionExists' => "U are already subscribed to our newsletter!",
            'subscriptionSuccess' => "Your subscription has been confirmed! Thank you for joining our newsletter.",
            'pendingSubscription' => "Please check your email to verify your subscription.",
            'resendConfirmation' => 'Confirmation link expired. We’ve sent you a new confirmation email.',
            'userDoesNotExists' => "User with this email does not exist. Please register.",
            'incorrectPassword' => "Password is not correct, please check again.",
            'unverifiedAccount' => "Account not verified, please check your email.",
            "userInformationUpdateSuccess" => "User information update successfully!",
            "userInformationUpdateError" => "Error while updating user information!",
            "passwordsNotMatching" => "Passwords must match!",
            "newPasswordIsOldPassword" => "New password can't be old password!",
            "productWishlistAddSuccess" => "Product has been successfully added to your wishlist!",
            "productWishlistAddError" => "Error while adding the product!",
            "productWishlistRemovedSuccess" => "Product successfully removed!",
            "productWishlistRemovedError" => "Error while removing the product!",
            "nameDoesNotMatch" => "Entered name does not match the users first name!",
            "lastNameDoesNotMatch" => "Entered last name does not match the users last name!",
            "emailDoesNotMatch" => "Entered email does not match the users email!",
            "productReviewSuccess" => "Review submitted successfully!",
            "productReviewError" => "Error while submitting review!",
            "reviewAlreadySubmitted" => "You have already submitted review for this product!",
            'review' => "Review must be between 10 and 50 characters.",
            'categoryNameExists' => "Category name already exists, try another one!",
            'categoryAddSuccess' => "Category added successfully!",
            'categoryAddError' => "Error while adding the category!",
            "productName" => "Please enter product name!",
            "productDescription" => "Please enter product description!",
            'categorySelect' => "Please select category name!",
            'genderSelect' => "Please select gender name!",
            'colorSelect' => "Please select color name!",
            'sizeSelect' => "Please select the size!",
            'priceEnter' => "Please enter the price!",
            'discountSelect' => "Please select discount value!",
            'priceStartDate' => "Please enter price start date!",
            'priceEndDate' => "Please enter price end date!",
            "productDeletedSuccess" => "Product deleted successfully!",
            "productDeleteError" => "Failed to delete the product.",
            "userDeleteSuccess" => "User deleted successfully!",
            "userDeleteError" => "Failed to delete the user.",
            "userMatchesRole" => "User already has that role!",
            "userRoleUpdateSuccess" => "User role updated!",
            'userRoleUpdateError' => "Failed to edit user role!",
            'fieldCantBeEmpty' => "This field can't be empty!",
            "productInsertSuccess" => "Product added successfully!",
            "productInsertError" => "Error while adding product!",
            "productEditSuccess" => "Product successfully edited!",
            "productEditError" => "Error while edditing product!",
            "orderSubmitSuccess" => "Order successfully submited!",
            "orderSubmitError" => "Error while submitting order!",
            'streetName' => "Street name must contain only words starting with a capital letter, each up to 12 characters long.",
            'streetNumber' => "Street number must start with digits and may end with a single lowercase letter (e.g., 25 or 25a).",
            'phoneNumber' => "Phone number must be from 8 to 10 digits with no spaces or symbols.",
            'country' => "Please select a country!",
            'city' => "Please select a city",
            'orderDeleteSuccess' => "Order successfully deleted!",
            'orderDeleteError' => "Error while deleting the order!",
            'orderInformationUpdateSuccess' => "Order information updated successfully!",
            'orderInformationUpdateError' => "Error while updating order information!",
            'orderItemDeleteSuccess' => "Item successfully deleted from the order!",
            'orderItemDeleteError' => "Error while deleting item from the order!",
            'orderItemUpdateSuccess' => "Order item successfully updated!",
            'orderItemUpdateError' => "Error while updating order item!"
        ];

        return $messages[$field] ?? null;
    }

    function testFields($field, $value) {
        $regex = getRegex($field);
        if (!$regex) {
            throw new Exception("No regex defined for field: $field");
        }
        return (bool) preg_match($regex, $value);
    }

    function getTopDiscount(){
        global $conn;
        $query = "SELECT d.id_discount, d.value, d.date_start, d.date_finish 
        FROM discount d
        INNER JOIN product_discount pd ON pd.id_discount = d.id_discount
        WHERE pd.id_discount = d.id_discount
        ORDER BY d.value DESC LIMIT 1";

        
        $result = $conn->query($query)->fetch();
        return $result;
    }

    function getNewArrivals($id_gender){
        global $conn;
        
        $query = "SELECT 
                p.id_product AS id, 
                p.name,
                p.cover_photo,
                g.id_gender, 
                d.value AS discount_value,
                d.date_start,
                d.date_finish,
                pr.value AS oldPrice,
                COALESCE(pr.value * (1 - d.value / 100), pr.value) AS price
            FROM products p
            LEFT JOIN gender g ON p.id_gender = g.id_gender
            LEFT JOIN product_discount pd ON p.id_product = pd.id_product
            LEFT JOIN discount d ON pd.id_discount = d.id_discount
            LEFT JOIN (
                SELECT id_product, value
                FROM price
                WHERE date_finish IS NULL
            ) pr ON p.id_product = pr.id_product
            WHERE p.is_active = 1
            ORDER BY p.id_product DESC
            LIMIT 10";
    
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $products = $stmt->fetchAll();

        $filteredProducts = [];

        if(!empty($id_gender))
        {
            foreach($products as $p){
                if($p->id_gender == $id_gender){
                    array_push($filteredProducts, $p);
                }
            }
            return $filteredProducts;
        }
        else
        {
            return $products;
        }
    }

    function getBestSellers(){
        global $conn;

        $query = "SELECT 
                        p.id_product AS id, 
                        p.name, 
                        p.cover_photo, 
                        g.id_gender, 
                        d.value AS discount_value, 
                        d.date_start, 
                        d.date_finish, 
                        pr.value AS oldPrice, 
                        COALESCE(pr.value * (1 - d.value / 100), pr.value) AS price, 
                        SUM(pc.quantity) AS total_quantity_sold
                    FROM products p
                    LEFT JOIN gender g ON p.id_gender = g.id_gender
                    LEFT JOIN product_discount pd ON p.id_product = pd.id_product
                    LEFT JOIN discount d ON pd.id_discount = d.id_discount
                    LEFT JOIN (
                        SELECT id_product, value
                        FROM price
                        WHERE date_finish IS NULL
                    ) pr ON p.id_product = pr.id_product
                    LEFT JOIN product_cart pc ON p.id_product = pc.id_product
                    WHERE p.is_active = 1
                    GROUP BY 
                        p.id_product, 
                        p.name, 
                        p.cover_photo, 
                        g.id_gender, 
                        d.value, 
                        d.date_start, 
                        d.date_finish, 
                        pr.value
                    ORDER BY total_quantity_sold DESC, p.id_product DESC
                    LIMIT 12";

        $result = $conn->query($query)->fetchAll();
        return $result;
    }


    function hashPassword($password, $algorithm = PASSWORD_DEFAULT){
        return password_hash($password, $algorithm);
    }

    function generateToken ($length = 32){
        global $conn;

        $token_value = bin2hex(random_bytes($length));
        $date = time();
        $expires_at = date("Y-m-d H:i:s", $date + (10*60));

        $query = "INSERT INTO token (token_value, expires_at)
                VALUES (:token_value, :expires_at)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':token_value', $token_value);
        $stmt->bindParam(':expires_at', $expires_at);
        $stmt->execute();
    }

    function userExists($email){
        global $conn;
        $query = "SELECT COUNT(*) FROM user WHERE email = :email";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    function getUserInformation($identifier) {
        global $conn;
    
        if (is_numeric($identifier)) {
            $query = "SELECT id_user, first_name, last_name, email, password, verified, profile_picture, id_role 
                      FROM user WHERE id_user = :identifier";
        } else {
            $query = "SELECT id_user, first_name, last_name, email, password, verified, profile_picture, id_role 
                      FROM user WHERE email = :identifier";
        }
    
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':identifier', $identifier);
        $stmt->execute();
    
        return $stmt->fetch();
    }
    
	function emailLogo() {
        return "
            <a href='https://coloshop.infinityfreeapp.com/index.php?page=home'>
                <img src='https://coloshop.infinityfreeapp.com/assets/images/website_logo.png'
                    alt='Site Logo'
                    style='width:150px;height:50px;'>
            </a>
        ";
    }

    function sendVerificationEmail($email, $token) {
        $mail = new PHPMailer(true);
        
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'coloshop732@gmail.com';
            $mail->Password = 'oorm oerz jrgy soho';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
    
            $mail->setFrom('coloshop732@gmail.com', 'ColoShop');
            $mail->addAddress($email);
    
            $logo = emailLogo();
            
            $mail->isHTML(true);
            $mail->Subject = 'Email Verification';
            $mail->Body = "
            	$logo
                <p>Click the link below to verify your account:</p>
                <a href='https://coloshop.infinityfreeapp.com/index.php?page=verify&token=$token'>
                    Verify Email
                </a>
            ";
            
            $mail->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

    function sendVerificationEmailNewsletter($email, $token) {
        $mail = new PHPMailer(true);
        
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'coloshop732@gmail.com';
            $mail->Password = 'oorm oerz jrgy soho';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
    
            $mail->setFrom('coloshop732@gmail.com', 'ColoShop');
            $mail->addAddress($email);
            
            $logo = emailLogo();
    
            $mail->isHTML(true);
            $mail->Subject = 'Newsletter Verification';
            $mail->Body = "
            	$logo
            	<p>Click the link below to verify your newsletter subscription: <p>
                <a href='https://coloshop.infinityfreeapp.com/index.php?page=verifySubscription&token=$token'>
                	Verify Subscription
                </a>
            ";
            
            $mail->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

    function sendContactConfirmationEmail($userEmail, $userName) {
        $mail = new PHPMailer(true);
        
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'coloshop732@gmail.com';
            $mail->Password = 'oorm oerz jrgy soho';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('coloshop732@gmail.com', 'ColoShop');
            $mail->addAddress($userEmail, $userName);
            
            $logo = emailLogo();

            $mail->isHTML(true);
            $mail->Subject = 'We received your message!';
            $mail->Body = "
            	$logo
                <p>Hi <strong>$userName</strong>,</p>
                <p>Thank you for contacting ColoShop. We've received your message and will get back to you as soon as possible.</p>
                <p><em>This is an automated message, please do not reply to this email.</em></p>
            ";

            $mail->send();
        } catch (Exception $e) {
            echo "Confirmation email could not be sent. Error: {$mail->ErrorInfo}";
        }
    }

    function sendContactToAdmin($name, $email, $subject, $message) {
        $mail = new PHPMailer(true);
        
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'coloshop732@gmail.com';
            $mail->Password = 'oorm oerz jrgy soho';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('coloshop732@gmail.com', 'Contact Form');
            $mail->addAddress('coloshop732@gmail.com', 'ColoShop Admin');
            $mail->addReplyTo($email, $name);

            $mail->isHTML(true);
            $mail->Subject = "New Contact Message: $subject";
            $mail->Body = "
                <h3>Message from contact form</h3>
                <p><strong>Name:</strong> $name</p>
                <p><strong>Email:</strong> $email</p>
                <p><strong>Subject:</strong> $subject</p>
                <p><strong>Message:</strong><br>" . nl2br($message) . "</p>
            ";

            $mail->send();
        } catch (Exception $e) {
            echo "Message to admin could not be sent. Error: {$mail->ErrorInfo}";
        }
    }

    function getLastToken(){
        global $conn;

        $query = "SELECT id_token, token_value, created_at, expires_at FROM token ORDER BY id_token DESC LIMIT 1";

        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    function registration($first_name, $last_name, $email, $password){
        global $conn;

        $hashedPassword = hashPassword($password, PASSWORD_DEFAULT);

        $id_token = getLastToken()->id_token;

        $query = "INSERT INTO user (first_name, last_name, email, password, id_token)
                VALUES (:first_name, :last_name, :email, :password, :id_token)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':id_token', $id_token);
        $stmt->execute();
    }

    function verifyUser($id_token){
        global $conn;

        $query = "UPDATE user SET verified = 1 WHERE id_token = :id_token";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_token', $id_token);
        $stmt->execute();
    }

    function verifySubscription($id_token){
        global $conn;

        $query = "UPDATE newsletter SET subscribed = 1 WHERE id_token = :id_token";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_token', $id_token);
        $stmt->execute();
    }

	function getTokenByUserId($id_user) {
        global $conn;
        
        $query = "SELECT t.* FROM token t 
                INNER JOIN user u ON t.id_token = u.id_token 
                WHERE u.id_user = :id_user";
        
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_user', $id_user);
        $stmt->execute();
        
        return $stmt->fetch();
    }

    function getTokenInformation($token_value){
        global $conn;

        $query = "SELECT id_token, token_value, created_at, expires_at FROM token WHERE token_value = :token_value";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':token_value', $token_value);
        $stmt->execute();
        return $stmt->fetch();
    }

    function getTokenInformationById($id_token){
        global $conn;

        $query = "SELECT id_token, token_value, created_at, expires_at FROM token WHERE id_token = :id_token";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_token', $id_token);
        $stmt->execute();
        return $stmt->fetch();
    }

    function userStatus($id_token){
        global $conn;

        $query = "SELECT verified, id_token, email FROM user WHERE id_token = :id_token";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_token', $id_token);
        $stmt->execute();
        return $stmt->fetch();
    }

    function newsletterStatus($id_token){
        global $conn;

        $query = "SELECT subscribed, id_token, email FROM newsletter WHERE id_token = :id_token";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_token', $id_token);
        $stmt->execute();
        return $stmt->fetch();
    }

    function updateUserToken($id_token, $email){
        global $conn;

        $query = "UPDATE user SET id_token = :id_token WHERE email = :email";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_token', $id_token);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
    }

    function updateNewsletterToken($id_token, $email){
        global $conn;

        $query = "UPDATE newsletter SET id_token = :id_token WHERE email = :email";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_token', $id_token);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
    }

    function encodeMessage($text) {
        return base64_encode(json_encode($text));
    }
    
    function decodeMessage($text){
        return json_decode(base64_decode($text));
    }

    function getBreadcrumbTitle($page) {
        switch ($page) {
            case 'user':
                return 'Profile';
            case 'shop':
                return 'Shop';
            case 'contact':
                return 'Contact Us';
            case 'product':
                return 'Product Details';
            default:
                return ucfirst($page);
        }
    }

    function updateUserInformation($id_user, $first_name, $last_name, $email, $password, $profile_picture) {
        global $conn;
    
        $query = "UPDATE user SET first_name = :first_name, last_name = :last_name, email = :email";

        if (!empty($password)) {
            $query .= ", password = :password";
        }
        
        if ($profile_picture) {
            $query .= ", profile_picture = :profile_picture";
        }
        
        $query .= " WHERE id_user = :id_user";
    
        $stmt = $conn->prepare($query);
        
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':email', $email);
        
        if (!empty($password)) {
            $stmt->bindParam(':password', $password);
        }
        
        if ($profile_picture) {
            $stmt->bindParam(':profile_picture', $profile_picture);
        }
        
        $stmt->bindParam(':id_user', $id_user);
        
        return $stmt->execute();
    }

    function selectWishlistedProducts($id_user) {
        global $conn;
        $query = "SELECT id_product FROM wishlist WHERE id_user = :id_user";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $productIds = [];
        foreach ($result as $row) {
            $productIds[] = $row['id_product'];
        }

        return $productIds;
    }

    function addProductToWishlist($id_user, $id_product){
        global $conn;

        $query = "INSERT INTO wishlist (id_user, id_product)
                VALUES (:id_user, :id_product)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_user', $id_user);
        $stmt->bindParam(':id_product', $id_product);
        
        if($stmt->execute()){
            return true;
        }
        else{
            return false;
        }
    }

    function deleteProductFromWishlist($id_user, $id_product){
        global $conn;

        $query = "DELETE FROM wishlist WHERE id_user = :id_user AND id_product = :id_product";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_user', $id_user);
        $stmt->bindParam(':id_product', $id_product);
        
        if($stmt->execute()){
            return true;
        }
        else{
            return false;
        }
    }

    function getWishlistProductsCount($id_user){
        global $conn;

        $query = "SELECT COUNT(DISTINCT id_product) AS total
        FROM wishlist w
        WHERE w.id_user = :id_user";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(":id_user", $id_user);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    function getWishlistProducts($id_user, $limit, $offset){
        global $conn;
        
        $limit = (int)$limit;
        $offset = (int)$offset;

        $query = "
            SELECT DISTINCT
                p.id_product AS id, 
                p.name,
                p.cover_photo, 
                d.value AS discount_value,
                d.date_start,
                d.date_finish,
                pr.value AS oldPrice,
                COALESCE(pr.value * (1 - d.value / 100), pr.value) AS price
            FROM products p
            LEFT JOIN product_discount pd ON p.id_product = pd.id_product
            LEFT JOIN discount d ON pd.id_discount = d.id_discount
            LEFT JOIN (
                SELECT id_product, value
                FROM price
                WHERE date_finish IS NULL
            ) pr ON p.id_product = pr.id_product
            LEFT JOIN product_size ps ON ps.id_product = p.id_product
            LEFT JOIN size s ON ps.id_size = s.id_size
            LEFT JOIN product_color pc ON p.id_product = pc.id_product
            LEFT JOIN color c ON pc.id_color = c.id_color
            LEFT JOIN wishlist w ON p.id_product = w.id_product
            WHERE w.id_product = p.id_product AND w.id_user = :id_user
            ORDER BY p.id_product ASC
            LIMIT $limit OFFSET $offset";

            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id_user', $id_user);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getProductReviewsInformation($id_product){
        global $conn;


        $query = "SELECT u.id_user, u.first_name, u.last_name, u.profile_picture, c.date, c.content, c.rating 
        FROM user u INNER JOIN comment c ON u.id_user = c.id_user WHERE c.id_product = :id_product";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_product', $id_product);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getProductReviews2($id_product, $limit, $offset){
        global $conn;

        $limit = (int)$limit;
        $offset = (int)$offset;

        $query = "SELECT u.id_user, u.first_name, u.last_name, u.profile_picture, c.id_comment, c.date, c.content, c.rating 
        FROM user u 
        INNER JOIN comment c ON u.id_user = c.id_user 
        WHERE c.id_product = :id_product
        ORDER BY c.id_comment ASC
        LIMIT $limit OFFSET $offset";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_product', $id_product);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getProductReviewCount($id_product){
        global $conn;

        $query = "SELECT COUNT(DISTINCT id_comment) AS total
        FROM comment c
        WHERE c.id_product = :id_product";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(":id_product", $id_product);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    function insertProductReview($id_user, $id_product, $content, $rating){
        global $conn;

        $query = "INSERT INTO comment (id_user, id_product, content, rating)
                VALUES (:id_user, :id_product, :content, :rating)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_user', $id_user);
        $stmt->bindParam(':id_product', $id_product);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':rating', $rating);
        
        if($stmt->execute()){
            return true;
        }
        else{
            return false;
        }
    }

    function checkReview($id_user, $id_product){
        global $conn;

        $query = "SELECT COUNT(*) FROM comment WHERE id_user = :id_user AND id_product = :id_product";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_user', $id_user);
        $stmt->bindParam(':id_product', $id_product);
    
        $stmt->execute();

        return $stmt->fetchColumn() > 0;
    }

    function getProductInformation($id_product){
        global $conn;

        $query = "SELECT p.id_product, p.name, p.is_active, p.description, p.cover_photo, 
            ca.id_categories, ca.name AS categoryName, 
            g.id_gender, 
            co.id_color,
            s.id_size, 
            pr.value, pr.date_start, pr.date_finish, 
            d.id_discount, d.value AS discountValue
            FROM products p 
            LEFT JOIN categories ca ON p.id_categories = ca.id_categories
            LEFT JOIN gender g ON p.id_gender = g.id_gender
            LEFT JOIN product_color pc ON p.id_product = pc.id_product
            LEFT JOIN color co ON pc.id_color = co.id_color
            LEFT JOIN product_size ps ON p.id_product = ps.id_product
            LEFT JOIN size s ON ps.id_size = s.id_size
            LEFT JOIN (
                SELECT id_product, value, date_start, date_finish
                FROM price
                WHERE date_finish IS NULL
            ) pr ON p.id_product = pr.id_product
            LEFT JOIN product_discount pd ON p.id_product = pd.id_product
            LEFT JOIN discount d ON pd.id_discount = d.id_discount
            WHERE p.id_product = :id_product";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_product', $id_product);
        $stmt->execute();
        $product = $stmt->fetch();

        if(!$product){
            return null;
        }

        $stmt2 = $conn->prepare("SELECT picture_path FROM pictures WHERE id_product = :id_product");
        $stmt2->bindParam(':id_product', $id_product);
        $stmt2->execute();

        $product->additional_pictures = $stmt2->fetchAll(PDO::FETCH_COLUMN);

        return $product;
    }


    function getOrCreateCartId($id_user){
        global $conn;
        $query = "SELECT id_cart FROM cart WHERE id_user = :id_user";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_user', $id_user);
    
        $stmt->execute();

        $idCart = $stmt->fetchColumn();

        if(!$idCart){
            $query = "INSERT INTO cart (id_user) VALUES (:id_user)";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id_user', $id_user);

            if($stmt->execute()){
                return $conn->lastInsertId();
            }
            else{
                return false;
            }
        }

        return $idCart;
    }

    function insertOrUpdateCartItem($id_cart, $id_product, $quantity, $size_value = null, $color_value = null) {
        global $conn;

        $id_size = null;
        if ($size_value !== null) {
            $stmt = $conn->prepare("SELECT id_size FROM size WHERE name = :size_value");
            $stmt->bindParam(':size_value', $size_value);
            $stmt->execute();
            $id_size = $stmt->fetchColumn();
        }

        $id_color = null;
        if ($color_value !== null) {
            $stmt = $conn->prepare("SELECT id_color FROM color WHERE name = :color_value");
            $stmt->bindParam(':color_value', $color_value);
            $stmt->execute();
            $id_color = $stmt->fetchColumn();
        }

        $stmt = $conn->prepare(
            "SELECT id_cart FROM product_cart 
            WHERE id_cart = :id_cart 
            AND id_product = :id_product 
            AND id_size = :id_size 
            AND id_color = :id_color"
        );
        $stmt->bindParam(':id_cart', $id_cart);
        $stmt->bindParam(':id_product', $id_product);
        $stmt->bindParam(':id_size', $id_size);
        $stmt->bindParam(':id_color', $id_color);
        $stmt->execute();

        $existingId = $stmt->fetchColumn();

        if ($existingId) {
            $query = "UPDATE product_cart 
                    SET quantity = quantity + :quantity
                    WHERE id_cart = :id_cart 
                    AND id_product = :id_product 
                    AND id_size = :id_size 
                    AND id_color = :id_color";

            $stmt = $conn->prepare($query);
            $stmt->bindParam(':quantity', $quantity);
            $stmt->bindParam(':id_cart', $id_cart);
            $stmt->bindParam(':id_product', $id_product);
            $stmt->bindParam(':id_size', $id_size);
            $stmt->bindParam(':id_color', $id_color);

            return $stmt->execute();
        } else {
            $query = "INSERT INTO product_cart (id_cart, id_product, quantity, id_size, id_color)
                    VALUES (:id_cart, :id_product, :quantity, :id_size, :id_color)";

            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id_cart', $id_cart);
            $stmt->bindParam(':id_product', $id_product);
            $stmt->bindParam(':quantity', $quantity);
            $stmt->bindParam(':id_size', $id_size);
            $stmt->bindParam(':id_color', $id_color);

            return $stmt->execute();
        }
    }


    function getCartProductsByCartId($id_cart) {
        global $conn;

        $query = "
            SELECT DISTINCT
                p.id_product AS id,
                p.name,
                p.cover_photo AS image,
                COALESCE(d.value, 0) AS discount,
                pr.value AS originalPrice,
                COALESCE(pr.value * (1 - COALESCE(d.value, 0) / 100), pr.value) AS discountedPrice,
                c.name AS category,
                s.name AS size,
                col.name AS color,
                pc.quantity
            FROM product_cart pc
            JOIN products p ON pc.id_product = p.id_product
            LEFT JOIN product_discount pd ON p.id_product = pd.id_product
            LEFT JOIN discount d ON pd.id_discount = d.id_discount
            LEFT JOIN (
                SELECT id_product, value
                FROM price
                WHERE date_finish IS NULL
            ) pr ON p.id_product = pr.id_product
            LEFT JOIN categories c ON p.id_categories = c.id_categories
            LEFT JOIN size s ON pc.id_size = s.id_size
            LEFT JOIN color col ON pc.id_color = col.id_color
            WHERE pc.id_cart = :id_cart
            ORDER BY pc.id_product_cart ASC";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_cart', $id_cart, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function deleteProductFromCart($id_cart, $id_product, $size_value = null, $color_value = null) {
        global $conn;

        $id_size = null;
        $id_color = null;

        if ($size_value !== null) {
            $stmt = $conn->prepare("SELECT id_size FROM size WHERE name = :size_value");
            $stmt->bindParam(':size_value', $size_value);
            $stmt->execute();
            $id_size = $stmt->fetchColumn();
        }

        if ($color_value !== null) {
            $stmt = $conn->prepare("SELECT id_color FROM color WHERE name = :color_value");
            $stmt->bindParam(':color_value', $color_value);
            $stmt->execute();
            $id_color = $stmt->fetchColumn();
        }

        $deleteQuery = "DELETE FROM product_cart 
                        WHERE id_cart = :id_cart 
                        AND id_product = :id_product 
                        AND id_size = :id_size 
                        AND id_color = :id_color";

        $stmt = $conn->prepare($deleteQuery);
        $stmt->bindParam(':id_cart', $id_cart);
        $stmt->bindParam(':id_product', $id_product);
        $stmt->bindParam(':id_size', $id_size);
        $stmt->bindParam(':id_color', $id_color);

        return $stmt->execute();
    }

    function getProductCartQuantity($id_cart, $id_product, $size_value = null, $color_value = null) {
        global $conn;

        $id_size = null;
        $id_color = null;

        if ($size_value !== null) {
            $stmt = $conn->prepare("SELECT id_size FROM size WHERE name = :size_value");
            $stmt->bindParam(':size_value', $size_value);
            $stmt->execute();
            $id_size = $stmt->fetchColumn();
        }

        if ($color_value !== null) {
            $stmt = $conn->prepare("SELECT id_color FROM color WHERE name = :color_value");
            $stmt->bindParam(':color_value', $color_value);
            $stmt->execute();
            $id_color = $stmt->fetchColumn();
        }

        $sql = "SELECT quantity FROM product_cart 
                WHERE id_cart = :id_cart 
                AND id_product = :id_product 
                AND id_size = :id_size 
                AND id_color = :id_color";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_cart', $id_cart);
        $stmt->bindParam(':id_product', $id_product);
        $stmt->bindParam(':id_size', $id_size);
        $stmt->bindParam(':id_color', $id_color);

        $stmt->execute();

        return $stmt->fetchColumn() ?? 0;
    }


    function updateProductFromCart($id_cart, $id_product, $quantity, $size_value = null, $color_value = null) {
        global $conn;

        $id_size = null;
        if ($size_value !== null) {
            $stmt = $conn->prepare("SELECT id_size FROM size WHERE name = :size_value");
            $stmt->bindParam(':size_value', $size_value);
            $stmt->execute();
            $id_size = $stmt->fetchColumn();
        }

        $id_color = null;
        if ($color_value !== null) {
            $stmt = $conn->prepare("SELECT id_color FROM color WHERE name = :color_value");
            $stmt->bindParam(':color_value', $color_value);
            $stmt->execute();
            $id_color = $stmt->fetchColumn();
        }

        if ($quantity === null || $id_size === null || $id_color === null) {
            return false;
        }

        $sql = "UPDATE product_cart 
                SET quantity = :quantity 
                WHERE id_cart = :id_cart 
                AND id_product = :id_product 
                AND id_size = :id_size 
                AND id_color = :id_color";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':id_cart', $id_cart);
        $stmt->bindParam(':id_product', $id_product);
        $stmt->bindParam(':id_size', $id_size);
        $stmt->bindParam(':id_color', $id_color);

        return $stmt->execute();
    }


    function getCities($id_country){
        global $conn;

        $stmt = $conn->prepare("SELECT id_city, name, zip_code FROM city WHERE id_country = :id_country");
        $stmt->bindParam(':id_country', $id_country);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getLastOrder(){
        global $conn;

        $query = "SELECT id_order FROM orders ORDER BY id_order DESC LIMIT 1";

        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch();
    }

    function insertOrder($id_user, $email, $first_name, $last_name, $street_name, $street_number, $id_city, $phone_number, $id_shipping_method, $paypal_order_id, $paypal_transaction_id){
        global $conn;

        $query = "INSERT INTO orders (id_user, email, first_name, last_name, street_name, street_number, id_city, phone_number, id_shipping_method, id_order_status, paypal_order_id, paypal_transaction_id)
                VALUES (:id_user, :email, :first_name, :last_name, :street_name, :street_number, :id_city, :phone_number, :id_shipping_method, 2, :paypal_order_id, :paypal_transaction_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_user', $id_user);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':street_name', $street_name);
        $stmt->bindParam(':street_number', $street_number);
        $stmt->bindParam(':id_city', $id_city);
        $stmt->bindParam(':phone_number', $phone_number);
        $stmt->bindParam(':id_shipping_method', $id_shipping_method);
        $stmt->bindParam(':paypal_order_id', $paypal_order_id);
        $stmt->bindParam(':paypal_transaction_id', $paypal_transaction_id);

        if($stmt->execute()){
            return true;
        }
        else{
            return false;
        }
    }

    function insertOrderItems($id_order, $id_product, $quantity, $size_value, $color_value){
        global $conn;

        $id_size = null;

        if ($size_value !== null) {
            $stmt = $conn->prepare("SELECT id_size FROM size WHERE name = :size_value");
            $stmt->bindParam(':size_value', $size_value);
            $stmt->execute();
            $id_size = $stmt->fetchColumn();
        }

        $id_color = null;

        if ($color_value !== null) {
            $stmt = $conn->prepare("SELECT id_color FROM color WHERE name = :color_value");
            $stmt->bindParam(':color_value', $color_value);
            $stmt->execute();
            $id_color = $stmt->fetchColumn();
        }

        $query = "INSERT INTO order_item (id_order, id_product, quantity, id_size, id_color)
                VALUES (:id_order, :id_product, :quantity, :id_size, :id_color)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_order', $id_order);
        $stmt->bindParam(':id_product', $id_product);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':id_size', $id_size);
        $stmt->bindParam(':id_color', $id_color);

        if($stmt->execute()){
            return true;
        }
        else{
            return false;
        }
    }


	function calculateOrderTotal($shippingMethodId, $cart) {
        global $conn;
        
        $subtotal = 0;

        foreach ($cart as $item) {
            $productQuery = "
                SELECT 
                    p.id_product,
                    pr.value AS originalPrice,
                    COALESCE(d.value, 0) AS discount,
                    COALESCE(pr.value * (1 - COALESCE(d.value, 0) / 100), pr.value) AS discountedPrice
                FROM products p
                LEFT JOIN product_discount pd ON p.id_product = pd.id_product
                LEFT JOIN discount d ON pd.id_discount = d.id_discount
                LEFT JOIN (
                    SELECT id_product, value
                    FROM price
                    WHERE date_finish IS NULL
                ) pr ON p.id_product = pr.id_product
                WHERE p.id_product = :id
            ";
            
            $stmt = $conn->prepare($productQuery);
            $stmt->bindParam(':id', $item['id']);
            $stmt->execute();
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($product) {
                $actualPrice = $product['discountedPrice'];
                $subtotal += $actualPrice * $item['quantity'];
            }
        }

        $shippingQuery = "SELECT price FROM shipping_method WHERE id_shipping_method = :id";
        $stmt = $conn->prepare($shippingQuery);
        $stmt->bindParam(':id', $shippingMethodId);
        $stmt->execute();
        $shipping = $stmt->fetch(PDO::FETCH_OBJ);
        
        $shippingCost = $shipping ? $shipping->price : 0;
        
        $total = $subtotal + $shippingCost;
        
        return $total;
    }


    function clearUserCart($id_cart){
        global $conn;

        $deleteQuery = "DELETE FROM product_cart WHERE id_cart = :id_cart";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->bindParam(':id_cart', $id_cart, PDO::PARAM_INT);
        return $deleteStmt->execute();
    }

    function getOrderStatusClass($statusName) {
        $status = strtolower($statusName);

        $statusClassMap = [
            'pending'    => 'btn-warning',
            'processing' => 'btn-info',
            'shipped'    => 'btn-primary',
            'delivered'  => 'btn-success',
            'cancelled'  => 'btn-danger',
            'refunded'   => 'btn-secondary',
            'on hold'    => 'btn-dark',
            'failed'     => 'btn-danger',
            'returned'   => 'btn-secondary'
        ];

        return $statusClassMap[$status] ?? 'btn-light';
    }
    
    function getUserOrders($id_user, $limit, $offset){
        global $conn;

        $limit = (int)$limit;
        $offset = (int)$offset;


        $query = "SELECT o.id_order AS orderId, o.id_user AS userId, o.email, o.first_name, o.last_name,
        o.street_name, o.street_number, o.phone_number, ci.name AS cityName, ci.zip_code, 
        co.name AS countryName, sm.name AS shippingMethodName, os.name AS orderStatusName, o.date  
        FROM orders o 
        INNER JOIN city ci ON o.id_city = ci.id_city
        INNER JOIN country co ON ci.id_country = co.id_country
        INNER JOIN shipping_method sm ON o.id_shipping_method = sm.id_shipping_method
        INNER JOIN order_status os ON o.id_order_status = os.id_order_status
        WHERE o.id_user = :id_user
        ORDER BY o.id_order ASC
        LIMIT $limit OFFSET $offset";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_user', $id_user);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    function getOrderInformation($id_order){
        global $conn;
        $query = "SELECT o.id_order AS orderId, o.id_user AS userId, o.email, o.first_name, o.last_name,
        o.street_name, o.street_number, o.phone_number, o.id_city, o.id_shipping_method, o.id_order_status, o.date  
        FROM orders o
        WHERE o.id_order = :id_order";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_order', $id_order);
        $stmt->execute();

        return $stmt->fetch();
    }

    function getUserOrdersCount($id_user){
        global $conn;

        $query = "SELECT COUNT(DISTINCT id_order) AS total
        FROM orders o
        WHERE o.id_user = :id_user";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(":id_user", $id_user);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    function getOrderItems($id_order){
        global $conn;
        $query = "SELECT id_order_item, id_order, id_product, quantity, id_size, id_color
        FROM order_item
        WHERE id_order = :id_order
        ORDER BY id_order_item ASC";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_order', $id_order);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    function getOrderShippingPrice($id_order) {
        global $conn;

        $query = "SELECT sm.price AS shippingPrice  
                FROM shipping_method sm 
                INNER JOIN orders o ON o.id_shipping_method = sm.id_shipping_method
                WHERE o.id_order = :id_order";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_order', $id_order, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return isset($result->shippingPrice) ? $result : (object)['shippingPrice' => 0.0];
    }


    function getOrderTotalPrice($id_order) {
        $shippingPrice = getOrderShippingPrice($id_order);
        $items = getOrderItems($id_order);
        $total = 0;

        foreach ($items as $item) {
            $priceData = getProductPrice($item->id_product);
            $total += $priceData['newPrice'] * $item->quantity;
        }

        $total += (float)$shippingPrice->shippingPrice;

        return $total;
    }

    function deleteProductComment($id_comment){
        global $conn;

        $query = "DELETE FROM comment WHERE id_comment = :id_comment";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_comment', $id_comment);
        
        if($stmt->execute()){
            return true;
        }
        else{
            return false;
        }
    }

    function subscriptionExists($email){
        global $conn;
        $query = "SELECT COUNT(*) FROM newsletter WHERE email = :email";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    function insertSubscription($email){
        global $conn;

        $id_token = getLastToken()->id_token;

        $query = "INSERT INTO newsletter (email, id_token)
                VALUES (:email, :id_token)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':id_token', $id_token);
        $stmt->execute();
    }

    function getNewsletterInformation($email){
        global $conn;
        $query = "SELECT id_newsletter, email, date_subscribed, subscribed, id_token FROM newsletter WHERE email = :email";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch();
    }

    function updateNewsletterStatus($token){
        global $conn;
        $query = "UPDATE newsletter SET subscribed = 0 WHERE id_token = (SELECT id_token FROM token WHERE token_value = :token)";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    function newsletterSubscriptionCheck($token){
        global $conn;
        $query = "SELECT subscribed 
                FROM newsletter 
                WHERE id_token = (SELECT id_token FROM token WHERE token_value = :token)";
        
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return ($result && $result['subscribed'] == 1);
    }

?>