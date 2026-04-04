<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require_once ABSOLUTE_PATH . 'vendor/autoload.php';

    function getProductsDashboard(){
        global $conn;
        $query = "SELECT p.id_product AS id, p.name, p.is_active AS status, c.name AS category_name, p.cover_photo, g.name AS gender_name 
        FROM products p INNER JOIN categories c ON p.id_categories = c.id_categories INNER JOIN gender g ON p.id_gender = g.id_gender";
        $result = $conn->query($query)->fetchAll();
        return $result;
    }

    function getUsersDashboard(){
        global $conn;
        $query = "SELECT u.id_user AS id, u.first_name, u.last_name, u.profile_picture, u.email, u.verified, r.name AS role_name
        FROM user u INNER JOIN role r ON u.id_role = r.id_role";
        $result = $conn->query($query)->fetchAll();
        return $result;
    }

    function insertCategoryName($name){
        global $conn;
        $query = "INSERT INTO categories (name) VALUES (:name)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':name', $name);

        try {
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return false;
        }
    }

    function discountExists($id_discount) {
        global $conn;

        $query = "SELECT COUNT(*) FROM discount WHERE id_discount = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$id_discount]);

        return $stmt->fetchColumn() > 0;
    }

    function deleteUser($id_user){
        global $conn;

        try {
            $conn->beginTransaction();

            $stmtWishlist = $conn->prepare("DELETE FROM wishlist WHERE id_user = :id_user");
            $stmtWishlist->bindParam(':id_user', $id_user);
            $stmtWishlist->execute();

            $stmtComments = $conn->prepare("DELETE FROM comment WHERE id_user = :id_user");
            $stmtComments->bindParam(':id_user', $id_user);
            $stmtComments->execute();

            $stmtCart = $conn->prepare("DELETE FROM cart WHERE id_user = :id_user");
            $stmtCart->bindParam(':id_user', $id_user);
            $stmtCart->execute();
            
            $stmtOrders = $conn->prepare("DELETE FROM orders WHERE id_user = :id_user");
            $stmtOrders->bindParam(':id_user', $id_user);
            $stmtOrders->execute();

            $stmtUser = $conn->prepare("DELETE FROM user WHERE id_user = :id_user");
            $stmtUser->bindParam(':id_user', $id_user);
            $stmtUser->execute();

            $conn->commit();
            return true;
        } catch (PDOException $e) {
            $conn->rollBack();
            return false;
        }
    }

	function deleteProduct($id_product){
        global $conn;

        try {
            $stmtCheck = $conn->prepare("SELECT COUNT(*) FROM order_item WHERE id_product = :id_product");
            $stmtCheck->bindParam(':id_product', $id_product);
            $stmtCheck->execute();
            $countInOrders = $stmtCheck->fetchColumn();

            if ($countInOrders > 0) {
                return false;
            }

            $conn->beginTransaction();

            $stmt = $conn->prepare("SELECT cover_photo FROM products WHERE id_product = :id_product");
            $stmt->bindParam(':id_product', $id_product);
            $stmt->execute();
            $cover = $stmt->fetchColumn();

            if ($cover && $cover !== DEFAULT_PRODUCT_PICTURE) {
                $coverPath = ABSOLUTE_PATH . $cover;
                if (file_exists($coverPath)) {
                    unlink($coverPath);
                }
            }

            $stmt = $conn->prepare("SELECT picture_path FROM pictures WHERE id_product = :id_product");
            $stmt->bindParam(':id_product', $id_product);
            $stmt->execute();
            $pictures = $stmt->fetchAll(PDO::FETCH_COLUMN);

            foreach ($pictures as $pic) {
                $picPath = ABSOLUTE_PATH . $pic;
                if (file_exists($picPath)) {
                    unlink($picPath);
                }
            }

            $tables = [
                'comment',
                'pictures',
                'price',
                'product_cart',
                'product_color',
                'product_discount',
                'product_size',
                'wishlist'
            ];

            foreach ($tables as $table) {
                $stmt = $conn->prepare("DELETE FROM $table WHERE id_product = :id_product");
                $stmt->bindParam(':id_product', $id_product);
                $stmt->execute();
            }

            $stmt = $conn->prepare("DELETE FROM products WHERE id_product = :id_product");
            $stmt->bindParam(':id_product', $id_product);
            $stmt->execute();

            $conn->commit();
            return true;

        } catch (Exception $e) {
            $conn->rollBack();
            error_log("Delete product error: " . $e->getMessage());
            return false;
        }
    }

    function deleteProductRelations($id_product, array $tables = []) {
        global $conn;

        if (empty($tables)) {
            return false;
        }

        try {
            foreach ($tables as $table) {
                $stmt = $conn->prepare("DELETE FROM $table WHERE id_product = :id_product");
                $stmt->bindParam(":id_product", $id_product);
                $stmt->execute();
            }

            return true;
        } catch (PDOException $e) {
            error_log("Error deleting product relations: " . $e->getMessage());
            return false;
        }
    }

    function deleteUserProfilePicture($filePath, $userId) {
        global $conn;

        $defaultProfile = DEFAULT_PROFILE_PICTURE;

        if (!$filePath || !$userId) {
            return false;
        }

        $fullPath = ABSOLUTE_PATH . $filePath;

        if (file_exists($fullPath)) {
            unlink($fullPath);
        }

        $stmt = $conn->prepare("UPDATE user SET profile_picture = :pic WHERE id_user = :id");
        $stmt->bindParam(":pic", $defaultProfile);
        $stmt->bindParam(":id", $userId);

        return $stmt->execute();
    }


    function deleteProductCoverPicture($filePath, $productId) {
        global $conn;

        $defaultCover = DEFAULT_PRODUCT_PICTURE;

        if (!$filePath || !$productId) return false;

        $fullPath = ABSOLUTE_PATH . $filePath;

        if (file_exists($fullPath)) {
            unlink($fullPath);
        }

        $stmt = $conn->prepare("UPDATE products SET cover_photo = :cover WHERE id_product = :id");
        $stmt->bindParam(":cover", $defaultCover);
        $stmt->bindParam(":id", $productId);

        return $stmt->execute();
    }

    function deleteSingleProductAdditionalPicture($productId, $filePath) {
        global $conn;

        if (!$productId || !$filePath) return false;

        $fullPath = ABSOLUTE_PATH . $filePath;
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
        
        $stmt = $conn->prepare("DELETE FROM pictures WHERE id_product = :id AND picture_path = :path");
        $stmt->bindParam(":id", $productId);
        $stmt->bindParam(":path", $filePath);

        return $stmt->execute();
    }



    function updateUserInformationDashboard($id_user, $id_role, $first_name, $last_name, $email, $profilePhoto, $verified){
        global $conn;

        $query = "UPDATE user SET verified = :verified";

        if(!empty($first_name)){
            $query .= ", first_name = :first_name";
        }

        if(!empty($last_name)){
            $query .= ", last_name = :last_name";
        }

        if(!empty($email)){
            $query .= ", email = :email";
        }

        if(!empty($profilePhoto)){
            $query .= ", profile_picture = :profile_picture";
        }

        if(!empty($id_role)){
            $query .= ", id_role = :id_role";
        }

        $query .= " WHERE id_user = :id_user";

        $stmt = $conn->prepare($query);

        $stmt->bindParam(':verified', $verified);


        if(!empty($first_name)){
            $stmt->bindParam(':first_name', $first_name);
        }

        if(!empty($last_name)){
            $stmt->bindParam(':last_name', $last_name);
        }

        if(!empty($email)){
            $stmt->bindParam(':email', $email);
        }

        if(!empty($profilePhoto)){
            $stmt->bindParam(':profile_picture', $profilePhoto);
        }

        if(!empty($id_role)){
            $stmt->bindParam(':id_role', $id_role);
        }

        $stmt->bindParam(':id_user', $id_user);

        try {
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());

            return false;
        }
    }

    function insertProduct($name, $description, $cover_photo, $id_categories, $id_gender, array $colorIds, array $sizeIds, $price, $id_discount, $additionalPictures = []){
        global $conn;

        try {
            $conn->beginTransaction();

            $query = "INSERT INTO products (name, description, cover_photo, id_categories, id_gender)
                    VALUES (:name, :description, :cover_photo, :id_categories, :id_gender)";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':cover_photo', $cover_photo);
            $stmt->bindParam(':id_categories', $id_categories);
            $stmt->bindParam(':id_gender', $id_gender);

            $stmt->execute();
            $productId = $conn->lastInsertId();

            if(!empty($additionalPictures)){
                $queryPic = "INSERT INTO pictures (picture_path, id_product)
                            VALUES (:path, :id_product)";
                $stmtPic = $conn->prepare($queryPic);

                foreach($additionalPictures as $pic){
                    $stmtPic->bindParam(':path', $pic);
                    $stmtPic->bindParam(':id_product', $productId);
                    $stmtPic->execute();
                }
            }

            $queryColor = "INSERT INTO product_color (id_product, id_color) VALUES (:id_product, :id_color)";

            $stmtColor = $conn->prepare($queryColor);

            foreach ($colorIds as $colorId) {
                $stmtColor->execute([
                    ':id_product' => $productId,
                    ':id_color'   => $colorId
                ]);
            }

            $querySize = "INSERT INTO product_size (id_product, id_size) VALUES (:id_product, :id_size)";
            
            $stmtSize = $conn->prepare($querySize);

            foreach ($sizeIds as $sizeId) {
                $stmtSize->execute([
                    ':id_product' => $productId,
                    ':id_size'    => $sizeId
                ]);
            }


            $queryPrice = "INSERT INTO price (value, id_product)
                        VALUES (:value, :id_product)";
            $stmtPrice = $conn->prepare($queryPrice);
            $stmtPrice->bindParam(':value', $price);
            $stmtPrice->bindParam(':id_product', $productId);

            $stmtPrice->execute();

            if ($id_discount > 0) {
                $queryDiscount = "INSERT INTO product_discount (id_product, id_discount)
                                VALUES (:id_product, :id_discount)";
                $stmtDiscount = $conn->prepare($queryDiscount);
                $stmtDiscount->bindParam(':id_product', $productId);
                $stmtDiscount->bindParam(':id_discount', $id_discount);

                $stmtDiscount->execute();
            }

            $conn->commit();
            return true;

        } catch (Exception $e) {
            $conn->rollBack();

            error_log($e->getMessage());
            return false;
        }
    }

    function updateProductInformation($id_product, $name, $description, $cover_photo, $id_categories, $id_gender, array $colorIds, array $sizeIds, $price, $id_discount, $is_active, $additionalPictures = []){
        global $conn;

        try {
            $conn->beginTransaction();

            $query = "UPDATE products SET name = :name, description = :description, cover_photo = :cover_photo,
            id_categories = :id_categories, id_gender = :id_gender, is_active = :is_active WHERE id_product = :id_product";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':cover_photo', $cover_photo);
            $stmt->bindParam(':id_categories', $id_categories);
            $stmt->bindParam(':id_gender', $id_gender);
            $stmt->bindParam(':is_active', $is_active);
            $stmt->bindParam(':id_product', $id_product);

            $stmt->execute();

            $conn->prepare("DELETE FROM product_color WHERE id_product = :id_product")->execute([':id_product' => $id_product]);

            $stmtColor = $conn->prepare("INSERT INTO product_color (id_product, id_color)VALUES (:id_product, :id_color)");

            foreach ($colorIds as $colorId) {
                $stmtColor->execute([
                    ':id_product' => $id_product,
                    ':id_color'   => $colorId
                ]);
            }

            $conn->prepare("DELETE FROM product_size WHERE id_product = :id_product")->execute([':id_product' => $id_product]);

            $stmtSize = $conn->prepare("INSERT INTO product_size (id_product, id_size) VALUES (:id_product, :id_size)");

            foreach ($sizeIds as $sizeId) {
                $stmtSize->execute([
                    ':id_product' => $id_product,
                    ':id_size'    => $sizeId
                ]);
            }

            $queryActive = "SELECT * FROM price 
                            WHERE id_product = :id_product AND date_finish IS NULL
                            ORDER BY date_start DESC LIMIT 1";

            $stmtActive = $conn->prepare($queryActive);
            $stmtActive->bindParam(':id_product', $id_product);
            $stmtActive->execute();
            $activePrice = $stmtActive->fetch();


            if ($activePrice && $activePrice->value != $price) {

                $queryFinish = "UPDATE price
                                SET date_finish = NOW()
                                WHERE id_price = :id_price";

                $stmtFinish = $conn->prepare($queryFinish);
                $stmtFinish->bindParam(':id_price', $activePrice->id_price);
                $stmtFinish->execute();

                $queryInsert = "INSERT INTO price (value, id_product)
                                VALUES (:value, :id_product)";

                $stmtInsert = $conn->prepare($queryInsert);
                $stmtInsert->bindParam(':value', $price);
                $stmtInsert->bindParam(':id_product', $id_product);
                $stmtInsert->execute();
            }


            $queryFindDiscount = "SELECT COUNT(*) FROM product_discount WHERE id_product = :id_product";
            $stmtFindDiscount = $conn->prepare($queryFindDiscount);
            $stmtFindDiscount->bindParam(':id_product', $id_product);
            $stmtFindDiscount->execute();

            if($stmtFindDiscount->fetchColumn() > 0){
                if ($id_discount > 0) {
                    $queryUpdateProductDiscount = "UPDATE product_discount SET id_discount = :id_discount WHERE id_product = :id_product";
                    $stmtUpdateProductDiscount = $conn->prepare($queryUpdateProductDiscount);
                    $stmtUpdateProductDiscount->bindParam(':id_product', $id_product);
                    $stmtUpdateProductDiscount->bindParam(':id_discount', $id_discount);

                    $stmtUpdateProductDiscount->execute();
                }
                else{
                    $queryDeleteProductDiscount = "DELETE FROM product_discount WHERE id_product = :id_product";
                    $stmtDeleteProductDiscount = $conn->prepare($queryDeleteProductDiscount);
                    $stmtDeleteProductDiscount->bindParam(':id_product', $id_product);

                    $stmtDeleteProductDiscount->execute();
                }
            }
            else{
                if ($id_discount > 0) {
                    $queryInsertProductDiscount = "INSERT INTO product_discount (id_discount, id_product)
                    VALUES (:id_discount, :id_product)";
                    $stmtInsertProductDiscount = $conn->prepare($queryInsertProductDiscount);
                    $stmtInsertProductDiscount->bindParam(':id_discount', $id_discount);
                    $stmtInsertProductDiscount->bindParam(':id_product', $id_product);
                    $stmtInsertProductDiscount->execute();
                }
            }

            if (!empty($additionalPictures)) {
                $queryInsertPic = "INSERT INTO pictures (id_product, picture_path) VALUES (:id_product, :picture_path)";
                $stmtInsertPic = $conn->prepare($queryInsertPic);
                foreach ($additionalPictures as $picPath) {
                    $stmtInsertPic->bindParam(':id_product', $id_product);
                    $stmtInsertPic->bindParam(':picture_path', $picPath);
                    $stmtInsertPic->execute();
                }
            }

            $conn->commit();
            return true;

        } catch (Exception $e) {
            $conn->rollBack();

            error_log($e->getMessage());
            return false;
        }
    }

    function getTableCount($table){
        global $conn;

        $query = "SELECT COUNT(*) as count FROM $table";
        $result = $conn->query($query)->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }

    function getRecenltyAddedProducts(){
        global $conn;

        $query = "
            SELECT 
                p.id_product AS id, 
                p.name,
                p.description,
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
            ORDER BY p.id_product DESC
            LIMIT 4
        ";

        $result = $conn->query($query)->fetchAll();
        return $result;
    }


    function getRecentUsers($ids){
        global $conn;

        try{
            $query = "SELECT u.id_user, u.first_name, u.last_name, u.profile_picture FROM user u";

            $placeholders = implode(',', $ids);
            $query .= " WHERE u.id_user IN ($placeholders) LIMIT 8";

            $stmt = $conn->prepare($query);

            $stmt->execute();

            return $stmt->fetchAll();
        }
        catch(Exception $e){
            $e->getMessage();
        }
    }

    function pageVisitPerPage($page_array, $page){

        $number = [];

        foreach($page_array as $p){
            if($page == $p){
                $number[] = $p;
            }
        }

        return count($number);
    }

    function pageVisitSum($page_array){
        return count($page_array);
    }

    function pageVisitPerPagePercentage($page_visit_number, $page_visit_sum){
        return round(($page_visit_number / $page_visit_sum) * 100, 2);
    }

    function getOrdersDashboard(){
        global $conn;
        $query = "SELECT o.id_order AS orderId, o.id_user AS userId, o.email, o.first_name, o.last_name,
        o.street_name, o.street_number, o.phone_number, os.name AS orderStatusName, o.date, o.paypal_order_id, o.paypal_transaction_id  
        FROM orders o INNER JOIN order_status os ON os.id_order_status = o.id_order_status";
        $result = $conn->query($query)->fetchAll();
        return $result;
    }

    function deleteOrder($id_order){
        global $conn;

        try {
            $conn->beginTransaction();

            $deleteOrderItemsQuery = "DELETE FROM order_item WHERE id_order = :id_order";
            $deleteOrderItemsStmt = $conn->prepare($deleteOrderItemsQuery);
            $deleteOrderItemsStmt->bindParam(':id_order', $id_order);
            $deleteOrderItemsStmt->execute();

            $deleteOrderQuery = "DELETE FROM orders WHERE id_order = :id_order";
            $deleteOrderStmt = $conn->prepare($deleteOrderQuery);
            $deleteOrderStmt->bindParam(':id_order', $id_order);
            $deleteOrderStmt->execute();

            $conn->commit();
            return true;

        } catch (Exception $e) {
            $conn->rollBack();

            error_log($e->getMessage());
            return false;
        }
    }

    function updateOrderInformation($id_order, $email, $first_name, $last_name, $street_name, $street_number, $id_city, $phone_number, $id_shipping_method, $id_order_status){
        global $conn;

        $query = "UPDATE orders SET id_city = :id_city, id_shipping_method = :id_shipping_method, id_order_status = :id_order_status";

        if(!empty($email)){
            $query .= ", email = :email";
        }

        if(!empty($first_name)){
            $query .= ", first_name = :first_name";
        }

        if(!empty($last_name)){
            $query .= ", last_name = :last_name";
        }

        if(!empty($street_name)){
            $query .= ", street_name = :street_name";
        }

        if(!empty($street_number)){
            $query .= ", street_number = :street_number";
        }

        if(!empty($phone_number)){
            $query .= ", phone_number = :phone_number";
        }

        $query .= " WHERE id_order = :id_order";

        $stmt = $conn->prepare($query);

        $stmt->bindParam(':id_city', $id_city);
        $stmt->bindParam(':id_shipping_method', $id_shipping_method);
        $stmt->bindParam(':id_order_status', $id_order_status);

        if(!empty($email)){
            $stmt->bindParam(':email', $email);
        }

        if(!empty($first_name)){
            $stmt->bindParam(':first_name', $first_name);
        }

        if(!empty($last_name)){
            $stmt->bindParam(':last_name', $last_name);
        }
        
        if(!empty($street_name)){
            $stmt->bindParam(':street_name', $street_name);
        }

        if(!empty($street_number)){
            $stmt->bindParam(':street_number', $street_number);
        }

        if(!empty($phone_number)){
            $stmt->bindParam(':phone_number', $phone_number);
        }

        $stmt->bindParam(':id_order', $id_order);

        try {
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());

            return false;
        }
    }

    function getOrderItemInformation($id_order_item){
        global $conn;
        $query = "SELECT id_order_item, id_order, id_product, quantity, id_size, id_color
        FROM order_item
        WHERE id_order_item = :id_order_item";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_order_item', $id_order_item);
        $stmt->execute();

        return $stmt->fetch();
    }

    function deleteOrderItem($id_order_item){
        global $conn;

        try{
            $query = "DELETE FROM order_item WHERE id_order_item = :id_order_item";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id_order_item', $id_order_item);
            $stmt->execute();
            return true;
        }
        catch(Exception $e){
            return false;
        }
    }

    function updateOrderItem($id_order_item, $quantity, $id_size, $id_color){
        global $conn;

        $query = "UPDATE order_item SET id_size = :id_size, id_color = :id_color";

        if(!empty($quantity)){
            $query .= ", quantity = :quantity";
        }

        $query .= " WHERE id_order_item = :id_order_item";


        $stmt = $conn->prepare($query);

        $stmt->bindParam(':id_size', $id_size);
        $stmt->bindParam(':id_color', $id_color);

        if(!empty($quantity)){
            $stmt->bindParam(':quantity', $quantity);
        }

        $stmt->bindParam(':id_order_item', $id_order_item);

        try {
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());

            return false;
        }
    }

    function getOrderCountDashboard(){
        global $conn;
        $query = "SELECT COUNT(DISTINCT id_order) AS order_count FROM orders";

        $stmt = $conn->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC)['order_count'];
    }

    function getOrderItemCount($id_order){
        global $conn;
        $query = "SELECT COUNT(*) AS item_count
                FROM order_item
                WHERE id_order = :id_order";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_order', $id_order);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC)['item_count'];
    }

    function getNewsletterSubscribers() {
            global $conn;
            $query = "SELECT n.email, u.first_name, u.last_name, t.token_value
                    FROM newsletter n
                    INNER JOIN token t ON n.id_token = t.id_token
                    LEFT JOIN user u ON n.email = u.email
                    WHERE n.subscribed = 1";
            $stmt = $conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function sendNewsletterEmail($subscriber, $subject, $content) {
        $email = $subscriber['email'];
        $firstName = $subscriber['first_name'] ?? '';
        $lastName = $subscriber['last_name'] ?? '';
        $token = $subscriber['token_value'];

        if (!empty($firstName) || !empty($lastName)) {
            $greeting = 'Hi ' . trim("$firstName $lastName") . ',';
        } else {
            $greeting = 'Hi there,';
        }
        
        $logo = emailLogo();

        $fullContent = "
            $logo
            <p>$greeting</p>
            $content
            <p style='font-size: 13px; color: #777;'>
                If you wish to no longer receive the newsletter, click here: 
                <a href='https://coloshop.infinityfreeapp.com/index.php?page=unsubscribeNewsletterCheck&token=$token'>Unsubscribe</a>.
            </p>
        ";

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

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $fullContent;

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Newsletter send error to {$email}: {$mail->ErrorInfo}");
            return false;
        }
    }

    function getNewsletterSubscribersDashboard(){
        global $conn;
        $query = "SELECT id_newsletter, email, date_subscribed, subscribed FROM newsletter";
        $result = $conn->query($query)->fetchAll();
        return $result;
    }

    function deleteNewsletter($id_newsletter){
        global $conn;

        try {
            $conn->beginTransaction();

            $stmtNewsletter = $conn->prepare("DELETE FROM newsletter WHERE id_newsletter = :id_newsletter");
            $stmtNewsletter->bindParam(':id_newsletter', $id_newsletter);
            $stmtNewsletter->execute();

            $conn->commit();
            return true;
        } catch (PDOException $e) {
            $conn->rollBack();
            return false;
        }
    }

    function getNewsletterInformation2($identifier) {
        global $conn;
    
        if (is_numeric($identifier)) {
            $query = "SELECT id_newsletter, email, date_subscribed, subscribed
                      FROM newsletter WHERE id_newsletter = :id_newsletter";
        } else {
            $query = "SELECT id_newsletter, email, date_subscribed, subscribed
                      FROM newsletter WHERE id_newsletter = :id_newsletter";
        }
    
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_newsletter', $identifier);
        $stmt->execute();
    
        return $stmt->fetch();
    }
    

    function updateNewsletterInformation($id_newsletter, $email, $subscribed){
        global $conn;

        $query = "UPDATE newsletter SET subscribed = :subscribed";

        if(!empty($email)){
            $query .= ", email = :email";
        }

        $query .= " WHERE id_newsletter = :id_newsletter";

        $stmt = $conn->prepare($query);

        $stmt->bindParam(':subscribed', $subscribed);

        if(!empty($email)){
            $stmt->bindParam(':email', $email);
        }

        $stmt->bindParam(':id_newsletter', $id_newsletter);

        try {
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());

            return false;
        }
    }

    function newsletterEmailExists($email, $excludeId = null) {
        global $conn;

        if ($excludeId) {
            $query = "SELECT COUNT(*) FROM newsletter WHERE email = ? AND id_newsletter != ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$email, $excludeId]);
        } else {
            $query = "SELECT COUNT(*) FROM newsletter WHERE email = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$email]);
        }

        return $stmt->fetchColumn() > 0;
    }

    function getProductSizes2($id_product) {
        global $conn;

        $query = "SELECT s.id_size, s.name 
                FROM size s
                INNER JOIN product_size ps ON s.id_size = ps.id_size
                WHERE ps.id_product = :id_product";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_product', $id_product);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    function getProductColors2($id_product) {
        global $conn;

        $query = "SELECT c.id_color, c.name 
                FROM color c
                INNER JOIN product_color pc ON c.id_color = pc.id_color
                WHERE pc.id_product = :id_product";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_product', $id_product);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }


function getProductComments($id_product) {
        global $conn;
        $query = "SELECT c.id_comment, c.date, c.content, c.rating, u.first_name, u.last_name, c.id_product
                FROM comment c
                LEFT JOIN user u ON c.id_user = u.id_user
                WHERE c.id_product = :id_product
                ORDER BY c.date DESC";
        
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_product', $id_product);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    function deleteComment($id_comment) {
        global $conn;

        $query = "DELETE FROM comment WHERE id_comment = :id_comment";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_comment', $id_comment, PDO::PARAM_INT);

        return $stmt->execute();
    }

    
    function getCommentById($id_comment) {
        global $conn;

        $query = "SELECT * FROM comment WHERE id_comment = :id_comment";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_comment', $id_comment, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }

    function updateCommentContent($id_comment, $content) {
        global $conn;

        $query = "UPDATE comment 
                SET content = :content 
                WHERE id_comment = :id_comment";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':id_comment', $id_comment, PDO::PARAM_INT);

        return $stmt->execute();
    }

?>