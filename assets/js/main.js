$(document).ready(function () {
    (function() {
        const options = {
            showButtonAfter: 200,
            animate: "normal",
            normal: { steps: 15, ms: 1000 / 60 },
            linear: { px: 80, ms: 1000 / 60 }
        };

        let stop = false;

        const button = document.createElement('a');
        button.innerHTML = '<i class="fas fa-arrow-up"></i>';
        button.id = "scrollToTopBtn";
        button.title = "Go to top";
        button.style.display = "none";
        document.body.appendChild(button);

        function scrollToTop() {
            let currentScroll = document.documentElement.scrollTop || document.body.scrollTop;
            if (currentScroll > 0 && !stop) {
                window.requestAnimationFrame(scrollToTop);
                window.scrollTo(0, currentScroll - currentScroll / 18);
            }
        }

        button.onclick = () => {
            if (options.animate !== false) {
                scrollToTop();
            } else {
                window.scrollTo(0, 0);
            }
        };

        function toggleButton() {
            if (document.body.scrollTop > options.showButtonAfter || document.documentElement.scrollTop > options.showButtonAfter) {
                button.style.display = "block";
            } else {
                button.style.display = "none";
            }
        }

        function stopAnimation(e) {
            stop = true;
            setTimeout(() => stop = false, 200);
        }

        let isScrolling = false;
        window.addEventListener('mousedown', function(e) {
            if (e.target === document.documentElement || e.target === document.body) {
                isScrolling = true;
                stopAnimation();
            }
        });

        window.addEventListener('mouseup', function() {
            isScrolling = false;
        });


        window.addEventListener('keydown' , (event) => {
            if(event.key === "ArrowDown" || event.key === "ArrowUp"){
                stopAnimation();
            }
        })

        window.history.scrollRestoration = "manual";

        window.onscroll = toggleButton;
        window.addEventListener("wheel", stopAnimation);
    })();

    var currentYear = new Date().getFullYear();
    document.getElementById('currentYear').innerHTML = currentYear;

    function getCurrentDate() {
        const now = new Date();
    
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
    
        return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
    }
    
    const currentUrlParams = new URLSearchParams(window.location.search);
    let currentPage2 = currentUrlParams.get('page');
    const currentGender = currentUrlParams.get('gender');
    
    if (!currentPage2) {
        currentPage2 = 'home';
    }

    document.querySelectorAll('.nav_link').forEach(link => {
        const url = new URL(link.href, window.location.origin);
        const page = url.searchParams.get('page');
        const gender = url.searchParams.get('gender');

        if (page === currentPage2) {
            if (page === 'shop') {
                if (gender === currentGender) {
                    link.classList.add('active-nav');
                }
            } else {
                link.classList.add('active-nav');
            }
        }
    });


    function fetchDealTimer() {
        $.ajax({
            url: "models/getTopDiscount.php",
            method: "GET",
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    if (response.time_difference === 0) {
                        $("#tajmer").html("<h2>Deal Ended</h2>");
                    } else {
                        updateTimerDisplay(response.time_difference);

                        startCountdown(response.time_difference);
                    }
                } else {
                    console.error(response.message);
                    $("#tajmer").html("<h2>" + response.message + "</h2>");
                }
            },
            error: function (xhr, status, error) {
                console.error("Error fetching deal timer:", error);
                console.error("Response Text:", xhr.responseText);
            }
        });
    }
    
    function startCountdown(timeDifference) {
        let countdown = setInterval(function () {
            if (timeDifference <= 0) {
                clearInterval(countdown);
                $("#tajmer").html("<h2>Deal Ended</h2>");
                return;
            }

            timeDifference--;
            
            updateTimerDisplay(timeDifference);
        }, 1000);
    }
    
    function updateTimerDisplay(timeDifference) {

        const days = Math.floor(timeDifference / (60 * 60 * 24));
        const hours = Math.floor((timeDifference % (60 * 60 * 24)) / (60 * 60));
        const minutes = Math.floor((timeDifference % (60 * 60)) / 60);
        const seconds = timeDifference % 60;
    
        $("#day").text(days);
        $("#hour").text(hours < 10 ? "0" + hours : hours);
        $("#minute").text(minutes < 10 ? "0" + minutes : minutes);
        $("#second").text(seconds < 10 ? "0" + seconds : seconds);
    }
    
    fetchDealTimer();


    function getMonthName(month){
        const monthNames = ["January","February","March","April","May","June","July","August","September","October","November","December"];

        return monthNames[month];
    }


    let userId = null;
    let userData = null;
    let userProducts = [];

    function getUserInformation() {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: "models/getUserInformation.php",
                type: "GET",
                dataType: "json",
                success: function (response) {
                    userId = response.user ? response.user.id_user : null;
                    userData = response.user || null;
                    userProducts = response.products;
                    resolve();
                },
                error: function (xhr, status, error) {
                    console.error("Error fetching user info:", error);
                    console.error("Response Text:", xhr.responseText);
                    reject(error);
                }
            });
        });
    }


    function populateUserForm() {
        if (!userData) return;

        $('#input_name').val(userData.first_name || '');
        $('#input_first_name').val(userData.first_name || '');
        $('#input_last_name').val(userData.last_name || '');
        $('#input_email').val(userData.email || '');
    }


    function productWrite(data, type = '', userId = null, userProducts = []){
        var ispis = '';

        for(let d in data){
            if(type === "slider"){
                ispis += `<div class="owl-item product_slider_item">`
            }

            ispis += `<div class="product-item">
            <div class="product discount product_filter">
                <div class="product_image">
                    <a href="index.php?page=single&id=${data[d]['id']}"><img src="${data[d]['cover_photo']}" alt="${data[d]['name']}"></a>
                </div>`

            if(url.searchParams.get("page") === 'wishlist'){
                if (userId) {
                    let isWishlisted = userProducts && userProducts.includes(parseInt(data[d]['id']));
                    let activeClass = isWishlisted ? 'active' : '';
                    let wishlistedAttr = isWishlisted ? 'true' : 'false';
                
                    ispis += `<a href='#' class='add-to-wishlist' data-product-id='${data[d]['id']}' data-wishlisted='${wishlistedAttr}'>
                                <div class="favorite favorite_left wishlist-icon ${activeClass}"></div>
                            </a>`;
                } else {
                    ispis += `<a href='index.php?page=login'>
                                <div class="favorite favorite_left wishlist-icon"></div>
                            </a>`;
                }
            }

            if(getCurrentDate() < data[d]['date_finish']){
                if(data[d]['discount_value'] > 0){
                    ispis+= `<div class="product_bubble product_bubble_right product_bubble_red d-flex flex-column align-items-center">
                            <span>-${data[d]['discount_value']}%</span>
                        </div>`
                }
            }

            ispis += `<div class="product_info">
                        <h6 class="product_name"><a href="index.php?page=single&id=${data[d]['id']}">${data[d]['name']}</a></h6>`

            ispis += `<div>`

            if(data[d]['discount_value'] > 0){
                ispis += `<span style="color: gray; text-decoration: line-through; font-weight:500">$${parseFloat(data[d]['oldPrice']).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span><br>`
            }

            ispis += `<span style="color: #fe4c50; font-weight:600">$${parseFloat(data[d]['price']).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span>
                        </div>
                    </div>
                </div>`

            ispis += `</div>`
            
            if(type === "slider"){
                ispis += `</div>`
            }
        }

        return ispis;
    }

    let currentPage = 1;
    const navEntries = performance.getEntriesByType("navigation");
    const isReload = navEntries.length && navEntries[0].type === "reload";

    const url = new URL(window.location.href);

    if (isReload) {
        url.searchParams.delete("currentPage");
        history.replaceState(null, '', url.href);
    } else {
        const urlParam = parseInt(url.searchParams.get("currentPage"));
        if (!isNaN(urlParam)) currentPage = urlParam;
    }

    let activePriceMin = null;
    let activePriceMax = null;


    function renderPagination(totalPages, currentPage, selector) {
        let paginationHtml = '';
        const currentUrl = new URL(window.location.href);
    
        if (currentPage > 1) {
            const prevUrl = new URL(currentUrl); 
            prevUrl.searchParams.set('currentPage', currentPage - 1);
            paginationHtml += `<a href="${prevUrl.href}" class="prev-page">Previous</a>`;
        }
    
        for (let i = 1; i <= totalPages; i++) {
            const pageUrl = new URL(currentUrl);
            pageUrl.searchParams.set('currentPage', i);
            paginationHtml += `<a href="${pageUrl.href}" class="page-number ${
                i === currentPage ? 'active' : ''
            }">${i}</a>`;
        }
    
        if (currentPage < totalPages) {
            const nextUrl = new URL(currentUrl);
            nextUrl.searchParams.set('currentPage', currentPage + 1);
            paginationHtml += `<a href="${nextUrl.href}" class="next-page">Next</a>`;
        }
    
        $(selector).html(paginationHtml);
    }

    function applyFiltersAndSorting(page = currentPage) {
        if (url.searchParams.get("page") !== 'shop') {
            return;
        }

        const searchParams = new URLSearchParams(window.location.search);

        const selectedCategories = [];
        const gender = parseInt(searchParams.get('gender'));
        const selectedSizes = [];
        const selectedColors = [];
        const selectedSort = $('.type_sorting_btn.active').data('value');

        let priceMin = activePriceMin;
        let priceMax = activePriceMax;

        $("#categories li i.selected").each(function () {
            selectedCategories.push(parseInt($(this).attr("id")));
        });
        $("#size li i.selected").each(function () {
            selectedSizes.push(parseInt($(this).attr("id")));
        });
        $("#color li i.selected").each(function () {
            selectedColors.push(parseInt($(this).attr("id")));
        });

        $.ajax({
            url: "models/getFilteredSortedProducts.php",
            type: "POST",
            data: {
                categories: selectedCategories,
                gender: gender,
                sizes: selectedSizes,
                colors: selectedColors,
                sort: selectedSort,
                priceMin: priceMin,
                priceMax: priceMax,
                page: page
            },
            dataType: "json",
            success: function (response) {
                if(response.products.length > 0)
                    {
                        $("#products-shop").html(productWrite(response.products, ''));
                        $('#products-shop').isotope('reloadItems').isotope();
                
                        renderPagination(response.totalPages, response.currentPage, '#pagination_shop');
                
                        $('#showing_results_shop').html(
                            `Showing page ${response.currentPage} of ${response.totalPages} (${response.totalProducts} products)`
                        );
                
                        currentPage = response.currentPage;
                    }
                    else if (page !== 1)
                    {
                        applyFiltersAndSorting(1);
                    }
                    else
                    {
                        const emptyProductsHTML = `
                        <div>
                            <h3>No products</h3>
                            <p>Currently there are no products.</p>
                        </div>`;
                
                        $("#products-shop").html(emptyProductsHTML);
                        $('#pagination_shop').empty();
                        $('#showing_results_shop').empty();
                    }
            },
            error: function (xhr, status, error) {
                console.error("Error fetching products: ", error);
                console.error("Response Text:", xhr.responseText);
            }
        });
    }

    $(".filter_button").on("click", function () {
        activePriceMin = $("#slider-range").slider("values", 0);
        activePriceMax = $("#slider-range").slider("values", 1);

        applyFiltersAndSorting(1);
    });

    $("#slider-range").slider({
        range: true,
        min: 1,
        max: 2000,
        values: [1, 2000],
        slide: function (event, ui) {

            $("#amount").val("$" + ui.values[0] + " - $" + ui.values[1]);

            $("#priceMinInput").val(ui.values[0]);
            $("#priceMaxInput").val(ui.values[1]);
        }
    });

    const SLIDER_MIN = $("#slider-range").slider("option", "min");
    const SLIDER_MAX = $("#slider-range").slider("option", "max");


    $("#priceMinInput").val($("#slider-range").slider("values", 0));
    $("#priceMaxInput").val($("#slider-range").slider("values", 1));


    $("#priceMinInput").on("input", function () {
        let minVal = parseInt($(this).val());

        if (isNaN(minVal)) {
            minVal = SLIDER_MIN;
        }

        if (minVal < SLIDER_MIN) {
            minVal = SLIDER_MIN;
        }

        let currentMax = $("#slider-range").slider("values", 1);
        if (minVal > currentMax) {
            minVal = currentMax;
        }

        $(this).val(minVal);

        $("#slider-range").slider("values", 0, minVal);

        $("#amount").val("$" + minVal + " - $" + currentMax);
    });


    $("#priceMaxInput").on("input", function () {
        let maxVal = parseInt($(this).val());

        if (isNaN(maxVal)) {
            maxVal = SLIDER_MAX;
        }

        if (maxVal > SLIDER_MAX) {
            maxVal = SLIDER_MAX;
        }

        let currentMin = $("#slider-range").slider("values", 0);
        if (maxVal < currentMin) {
            maxVal = currentMin;
        }

        $(this).val(maxVal);

        $("#slider-range").slider("values", 1, maxVal);

        $("#amount").val("$" + currentMin + " - $" + maxVal);
    });

    $("#amount").val("$" + $("#slider-range").slider("values", 0) + " - $" + $("#slider-range").slider("values", 1));

    $("#categories li i, #size li i, #color li i").click(function () {
        $(this).toggleClass("selected");
        applyFiltersAndSorting(1);
    });
        
    $('.type_sorting_btn').click(function () {
        $('.type_sorting_btn').removeClass('active');
        $(this).addClass('active');
    
        let sortText = $(this).find('span').text();
        $('.type_sorting_text').text(sortText);
    
        applyFiltersAndSorting(1);
    });

    $(document).on('click', '.pagination a', function (e) {
        e.preventDefault();
        const pageUrl = new URL($(this).attr('href'));
        const page = parseInt(pageUrl.searchParams.get('currentPage'));
        
        applyFiltersAndSorting(page);

        getWishlistProducts(page);

        getProductComments(page);

        getOrders(page);
    
        history.pushState(null, '', pageUrl.href);
    });

    applyFiltersAndSorting(currentPage);

    const regexPatterns = {
        name: /^[A-Z][a-zA-Z]{2,49}$/,
        email: /^[a-z]{5}[a-z0-9._,]*@[a-z]+\.[a-z]{2,}$/,
        subject: /^[a-zA-Z0-9\s]{3,100}$/,
        message: /^.{10,500}$/,
        password : /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/
    };

    function showToast(message, type = 'success') {
        const container = document.querySelector('.toast-container') || createToastContainer();
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
    
        const content = document.createElement('span');
        content.textContent = message;
    
        const closeButton = document.createElement('button');
        closeButton.textContent = '×';
        closeButton.className = 'toast-close';
        closeButton.onclick = () => slideOutToast(toast);
    
        const progressBar = document.createElement('div');
        progressBar.className = 'toast-progress';
    
        toast.appendChild(closeButton);
        toast.appendChild(content);
        toast.appendChild(progressBar);
    
        container.appendChild(toast);
    
        progressBar.style.animationDuration = '8s';
    
        const timeout = setTimeout(() => {
            slideOutToast(toast);
        }, 8000);
    
        closeButton.addEventListener('click', () => clearTimeout(timeout));
    }
    
    function createToastContainer() {
        const container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
        return container;
    }
    
    function slideOutToast(toast) {
        toast.classList.add('slide-out');
        toast.addEventListener('animationend', () => toast.remove());
    }

    function messageText(field){
        const messages = {
            fieldCanNotBeEmpty: "This field cannot be empty!",
            pleaseSelectAnOption: "Option must be selected!"
        };

        return messages[field] || null;
    }

    function displayErrors(errorMessages) {
        errorMessages.forEach(function (error) {
            if (error.errorSelector && error.errorMessage) {
                $(error.errorSelector).text(error.errorMessage).css('display', 'block');
            }
            
            if (error.inputSelector) {
                error.inputSelector.split(',').forEach(selector => {
                    $(selector.trim()).addClass('error');
                });
            }
        });
    }

    $('.form_input, textarea').on('focus', function () {
        $(this).removeClass('error');
    
        const errorTarget = $(this).data('error-target');
    
        if (errorTarget) {
            $(`#${errorTarget}`).hide();
        } else {
            const inputId = $(this).attr('id');
            const errorId = `#error_${inputId.replace('input_', '')}`;
            $(errorId).hide();
        }
    
        $('#verifyMessage').slideUp(1000);
    });

    function sendMessage() {
        $('#review_submit').on('click', function (e) {
            e.preventDefault();
    
            $('.form_input, textarea').removeClass('error');
            $('.error-message').text('');

            const allMessages = document.querySelectorAll('.error-message');
            allMessages.forEach(element => {
                element.style.display = "none"; 
            });
    
            $('#loading-overlay').addClass('show');
    
            const name = $('#input_name').val().trim();
            const email = $('#input_email').val().trim();
            const subject = $('#input_subject').val().trim();
            const message = $('#input_message').val().trim();
    
            const ajaxStartTime = performance.now();

            $.ajax({
                url: "models/sendMessage.php",
                type: "POST",
                data: {
                    name: name,
                    email: email,
                    subject: subject,
                    message: message
                },
                dataType: "json",
                success: function (response) {
                    const ajaxDuration = performance.now() - ajaxStartTime;
                    const totalDuration = ajaxDuration + 1000;
    
                    setTimeout(function () {
                        $('#loading-overlay').removeClass('show');
    
                        if (response.success) 
                        {
                            showToast(response.message, 'success');
                            $('form')[0].reset();
                        }
                        else
                        {
                            displayErrors(response.errors);
                            // showToast(response.message, 'error');
                        }
                    }, totalDuration);
                },
                error: function (xhr, status, error) {
                    console.error("Error:", error);
                    console.error("Response Text:", xhr.responseText);
    
                    const ajaxDuration = performance.now() - ajaxStartTime;
                    const totalDuration = ajaxDuration + 1000;
    
                    setTimeout(function () {
                        // showToast("An error occurred. Please try again later.", 'error');
                        $('#loading-overlay').removeClass('show');
                    }, totalDuration);
                }
            });
        });
    }
    
    sendMessage();


    function newArrivals(){
        $(document).ready(function(){
            $('.grid_sorting_button:first').click();
        })
        
        $('.grid_sorting_button:first').addClass('active');
    
        $('.grid_sorting_button').click(function(){
            var gender = $(this).attr('id');
    
            $('.grid_sorting_button').removeClass('active');
            $(this).addClass('active');
            
            $.ajax({
                url: "models/getNewArrivalsHome.php",
                type: "POST",
                data: {
                    gender : gender
                },
                dataType: "json",
                success: function (response) {
                    $(".product-grid").html(productWrite(response.products, ''));
                    $('.product-grid').isotope('reloadItems').isotope();
                },
                error: function (xhr, status, error) {
                    console.error("Error:", error);
                    console.error("Response Text:", xhr.responseText);
                }
            });
        })
    }

    newArrivals();


    function initSlider() {
        $(".product_slider").owlCarousel({
            loop: true,
            nav: false,
            dots: true,
            responsive: {
                0: { items: 1, slideBy: 1 },
                480: { items: 2, slideBy: 1 },
                768: { items: 3, slideBy: 1 },
                991: { items: 4, slideBy: 2 },
                1280: { items: 6, slideBy: 3 },
                1440: { items: 6, slideBy: 3 }
            }
        });
    
        $(".product_slider_prev").on("click", function () {
            $(".product_slider").trigger("prev.owl.carousel");
        });
    
        $(".product_slider_next").on("click", function () {
            $(".product_slider").trigger("next.owl.carousel");
        });
    }
    
    
    function loadBestSellers() {
        $.ajax({
            url: 'models/getBestSellers.php',
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                $(".owl-carousel").html(productWrite(response.products, "slider"));
                initSlider();
            },
            error: function (xhr, status, error) {
                console.error('AJAX error:', error);
            }
        });
    }
    
    loadBestSellers();

    const passwordInputs = document.querySelectorAll('.input_password');
    const toggleButtons = document.querySelectorAll('.toggle-password-btn');
    
    passwordInputs.forEach((passwordInput, index) => {
        const toggleButton = toggleButtons[index];
        const eyeIcon = toggleButton ? toggleButton.querySelector("i") : null;
        
        if (passwordInput && toggleButton) {
            passwordInput.addEventListener('input', function() {
                toggleButton.style.display = this.value.trim() ? "block" : "none";
            });
            
            toggleButton.addEventListener('click', function() {
                if (passwordInput.type === "password") {
                    passwordInput.type = "text";
                    if (eyeIcon) {
                        eyeIcon.classList.remove("fa-eye-slash");
                        eyeIcon.classList.add("fa-eye");
                    }
                } else {
                    passwordInput.type = "password";
                    if (eyeIcon) {
                        eyeIcon.classList.remove("fa-eye");
                        eyeIcon.classList.add("fa-eye-slash");
                    }
                }
            });
        }
    });
    
    function registerSubmit() {
        $('#register_submit').on('click', function (e) {
            e.preventDefault();
    
            $('.form_input, textarea').removeClass('error');
            $('.error-message').text('');

            const allMessages = document.querySelectorAll('.error-message');
            allMessages.forEach(element => {
                element.style.display = "none"; 
            });

            const passwordS = document.querySelectorAll('.input_password');
            const toggleButtonS = document.querySelectorAll('.toggle-password-btn');
            
            passwordS.forEach(element => {
                if(element.type === "text"){
                    element.type = "password";

                    toggleButtonS.forEach(element => {
                        const eye = element.querySelector('i');

                        if(eye){
                            eye.classList.remove("fa-eye");
                            eye.classList.add("fa-eye-slash");
                        }
                    });
                }
            });
    
            $('#loading-overlay').addClass('show');
    
            const firstName = $('#input_first_name').val().trim();
            const lastName = $('#input_last_name').val().trim();
            const email = $('#input_email').val().trim();
            const password = $('#input_password').val().trim();
    
            const ajaxStartTime = performance.now();
    
            $.ajax({
                url: "models/registration.php",
                type: "POST",
                data: {
                    firstName: firstName,
                    lastName: lastName,
                    email: email,
                    password: password
                },
                dataType: "json",
                success: function (response) {
                    const ajaxDuration = performance.now() - ajaxStartTime;
                    const totalDuration = ajaxDuration + 1500;
    
                    setTimeout(function () {
                        $('#loading-overlay').removeClass('show');
    
                        if (response.success) 
                        {
                            showToast(response.message, 'success');
                            $('form')[0].reset();
                            // toggleButton.style.display = "none";
                        }
                        else
                        {
                            displayErrors(response.errors);
                            // showToast(response.message, 'error');
                        }
                    }, totalDuration);
                },
                error: function (xhr, status, error) {
                    console.error("Error:", error);
                    console.error("Response Text:", xhr.responseText);
    
                    const ajaxDuration = performance.now() - ajaxStartTime;
                    const totalDuration = ajaxDuration + 1500;
    
                    setTimeout(function () {
                        // showToast("An error occurred. Please try again later.", 'error');
                        $('#loading-overlay').removeClass('show');
                    }, totalDuration);
                }
            });
        });
    }
    
    registerSubmit();


    function loginSubmit() {
        $('#login_submit').on('click', function (e) {
            e.preventDefault();
    
            $('.form_input, textarea').removeClass('error');
            $('.error-message').text('');

            const allMessages = document.querySelectorAll('.error-message');
            allMessages.forEach(element => {
                element.style.display = "none"; 
            });

            const passwordS = document.querySelectorAll('.input_password');
            const toggleButtonS = document.querySelectorAll('.toggle-password-btn');
            
            passwordS.forEach(element => {
                if(element.type === "text"){
                    element.type = "password";

                    toggleButtonS.forEach(element => {
                        const eye = element.querySelector('i');

                        if(eye){
                            eye.classList.remove("fa-eye");
                            eye.classList.add("fa-eye-slash");
                        }
                    });
                }
            });
    
            $('#loading-overlay').addClass('show');
    
            const email = $('#input_email').val().trim();
            const password = $('#input_password').val().trim();

            const errors = [];

            if(!email)
            {
                errors.push({
                    inputSelector: '#input_email',
                    errorSelector: '#error_email',
                    errorMessage: messageText('fieldCanNotBeEmpty')
                });
            }

            if(!password){
                errors.push({
                    inputSelector: '#input_password',
                    errorSelector: '#error_password',
                    errorMessage: messageText('fieldCanNotBeEmpty')
                });
            }

            if(errors.length > 0){

                setTimeout(function () {

                    displayErrors(errors);

                    $('#loading-overlay').removeClass('show');

                }, 1500);

                return;
            }
            
            const ajaxStartTime = performance.now();
    
            $.ajax({
                url: "models/login.php",
                type: "POST",
                data: {
                    email: email,
                    password: password
                },
                dataType: "json",
                success: function (response) {
                    const ajaxDuration = performance.now() - ajaxStartTime;
                    const totalDuration = ajaxDuration + 1500;
    
                    setTimeout(function () {
                        $('#loading-overlay').removeClass('show');
    
                        if (response.success) 
                        {
                            // showToast(response.message, 'success');
                            // $('form')[0].reset();
                            // toggleButton.style.display = "none";
                            window.location.href = response.location;
                        }
                        else
                        {
                            displayErrors(response.errors);
                            // showToast(response.message, 'error');
                        }
                    }, totalDuration);
                },
                error: function (xhr, status, error) {
                    console.error("Error:", error);
                    console.error("Response Text:", xhr.responseText);
    
                    const ajaxDuration = performance.now() - ajaxStartTime;
                    const totalDuration = ajaxDuration + 1500;
    
                    setTimeout(function () {
                        // showToast("An error occurred. Please try again later.", 'error');
                        $('#loading-overlay').removeClass('show');
                    }, totalDuration);
                }
            });
        });
    }

    loginSubmit();

    const $messageDiv = $('#verifyMessage');

    if ($messageDiv.length && $messageDiv.html().trim() !== '') {
        $messageDiv.fadeIn(1000);
    }


    var sidebar_links = document.querySelectorAll('.sidebar-link');

    sidebar_links.forEach(link => {

        var currentPage = new URLSearchParams(window.location.search).get('page');

        if (link.id === currentPage) {
            link.classList.add('active');
        }
    });

    function showConfirmModal(message, callback) {
        const modal = document.getElementById('confirmModal');
        const msg = document.getElementById('confirmMessage');
        const yesBtn = document.getElementById('confirmYes');
        const noBtn = document.getElementById('confirmNo');

        msg.textContent = message;
        modal.style.display = "flex";

        yesBtn.onclick = function() {
            modal.style.display = "none";
            callback(true);
        };

        noBtn.onclick = function() {
            modal.style.display = "none";
            callback(false);
        };
    }

    function removeProfilePicture() {
        const removeBtn = document.getElementById('remove_picture_btn');

        if (!removeBtn) return;

        removeBtn.addEventListener('click', function(){
            showConfirmModal("Are you sure you want to remove your profile picture?", function(confirmed){
                if(confirmed){
                    $('#loading-overlay').addClass('show');

                    const formData = new FormData();
                    formData.append('remove_picture', 1);

                    const ajaxStartTime = performance.now();

                    $.ajax({
                        url: "models/updateUserInfo.php",
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: "json",
                        success: function(response){
                            const ajaxDuration = performance.now() - ajaxStartTime;
                            const totalDuration = ajaxDuration + 1500;

                            setTimeout(function(){
                                $('#loading-overlay').removeClass('show');

                                if(response.success && response.profile_picture){
                                    $('.profile-pic').attr('src', response.profile_picture);
                                    showToast("Profile picture removed successfully.", 'success');

                                    // populateUserForm();
                                } else {
                                    showToast(response.message || "Failed to remove picture.", 'error');
                                }
                            }, totalDuration);
                        },
                        error: function(){
                            $('#loading-overlay').removeClass('show');
                            showToast("An error occurred. Please try again.", 'error');
                        }
                    });
                }
            });
        });
    }

    function updateUserInformation() {
        $('#edit_profile').on('click', function (e) {
            e.preventDefault();
    
            $('.form_input, textarea').removeClass('error');
            $('.error-message').text('');
            
            const allMessages = document.querySelectorAll('.error-message');
            allMessages.forEach(element => {
                element.style.display = "none"; 
            });

            const passwordS = document.querySelectorAll('.input_password');
            const toggleButtonS = document.querySelectorAll('.toggle-password-btn');
            
            passwordS.forEach(element => {
                if(element.type === "text"){
                    element.type = "password";

                    toggleButtonS.forEach(element => {
                        const eye = element.querySelector('i');

                        if(eye){
                            eye.classList.remove("fa-eye");
                            eye.classList.add("fa-eye-slash");
                        }
                    });
                }
            });
    
            $('#loading-overlay').addClass('show');

            const formData = new FormData($('#profileForm')[0]);
    
            const ajaxStartTime = performance.now();
    
            $.ajax({
                url: "models/updateUserInfo.php",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: "json",
                success: function (response) {
                    const ajaxDuration = performance.now() - ajaxStartTime;
                    const totalDuration = ajaxDuration + 1500;
    
                    setTimeout(function () {
                        $('#loading-overlay').removeClass('show');
    
                        if (response.success) 
                        {
                            showToast(response.message, 'success');
                            $('#profileForm')[0].reset();
                            // toggleButton.style.display = "none";

                            if (response.profile_picture) {
                                $('.profile-pic').attr('src', response.profile_picture);
                            }

                            if (response.first_name) {
                                $('.user-greeting').text('Hi ' + response.first_name + '!');
                            }

                            userData.first_name = response.first_name || userData.first_name;
                            userData.last_name = response.last_name || userData.last_name;
                            userData.email = response.email || userData.email;

                            populateUserForm();
                        }
                        else
                        {
                            displayErrors(response.errors);
                            // showToast(response.message, 'error');
                        }
                    }, totalDuration);
                },
                error: function (xhr, status, error) {
                    console.error("Error:", error);
                    console.error("Response Text:", xhr.responseText);
    
                    const ajaxDuration = performance.now() - ajaxStartTime;
                    const totalDuration = ajaxDuration + 1500;
    
                    setTimeout(function () {
                        // showToast("An error occurred. Please try again later.", 'error');
                        $('#loading-overlay').removeClass('show');
                    }, totalDuration);
                }
            });
        });
    }
    

    
    document.addEventListener('click', function(e) {
        const link = e.target.closest('.add-to-wishlist');
        if (!link) return;

        e.preventDefault();

        console.log("123");

        const icon = link.querySelector('.wishlist-icon');
        if (!icon) return;

        const productId = link.dataset.productId;
        if (!productId) return;

        const isWishlisted = link.dataset.wishlisted === 'true';

        console.log(isWishlisted);

        if (isWishlisted) {
            showConfirmModal("Do you want to remove this product from your wishlist?", function(confirmed) {
                if (!confirmed) return;

                icon.classList.remove('active');
                link.dataset.wishlisted = 'false';
                deleteFromWishlist(productId);
            });
        } else {
            icon.classList.add('active');
            link.dataset.wishlisted = 'true';
            addToWishlist(productId);
        }
    });

    
    function addToWishlist(productId){

        const id_product = productId;

        $.ajax({
            url: "models/addToWishlist.php",
            type: "POST",
            data: {
                id_product: id_product
            },
            dataType: "json",
            success: function (response) {
                
                if (response.success) 
                    {
                        showToast(response.message, 'success');
                    }
                else
                    {
                        showToast(response.message, 'error');;
                    }
            },
            error: function (xhr, status, error) {
                console.error("Error:", error);
                console.error("Response Text:", xhr.responseText);
            }
        });
    }

    function deleteFromWishlist(productId){

        const id_product = productId;

        $.ajax({
            url: "models/deleteFromWishlist.php",
            type: "POST",
            data: {
                id_product: id_product
            },
            dataType: "json",
            success: function (response) {
                if (response.success) 
                {
                    showToast(response.message, 'success');
    
                    if (window.location.href.includes('page=wishlist')) {
                        getWishlistProducts(currentPage);
                    }
                } 
                else 
                {
                    showToast(response.message, 'error');
                }
            },
            error: function (xhr, status, error) {
                console.error("Error:", error);
                console.error("Response Text:", xhr.responseText);
            }
        });
    }

    function getWishlistProducts(page = currentPage) {
        if (!userId) {
            return;
        }
        
        $.ajax({
            url: 'models/getWishlistProducts.php',
            method: 'POST',
            data: {
                page: page
            },
            dataType: 'json',
            success: function(response) {
                if (response.products.length > 0) {
                    $("#products-wishlist").html(productWrite(response.products, '', userId, userProducts));
                    $('#products-wishlist').isotope('reloadItems').isotope();
    
                    renderPagination(response.totalPages, response.currentPage, '#pagination_wishlist');
    
                    $('#showing_results_wishlist').html(
                        `Showing page ${response.currentPage} of ${response.totalPages} (${response.totalProducts} products)`
                    );
    
                    currentPage = response.currentPage;
                }
                else if (page !== 1) {
                    getWishlistProducts(1);
                }
                else {
                    const emptyWishlistHTML = `
                        <div class="empty-wishlist">
                            <h3>Your wishlist is empty</h3>
                            <p>You haven't added any products to your wishlist yet. Start exploring our collection and save your favorite items here.</p>
                            <a href="index.php?page=home" ><button class="browse-btn">Browse Products</button></a>
                        </div>
                    `;
                    $("#products-wishlist").html(emptyWishlistHTML);
                    $('#pagination_wishlist').empty();
                    $('#showing_results_wishlist').empty();
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', error);
                console.log(xhr.responseText);
            }
        });
    }

    function reviewWrite(data, reviewCount){
        var ispis = '';

        ispis+= `<div class="tab_title reviews_title">
                <h4>Reviews (${reviewCount})</h4>
            </div>`

        for(let d in data){

            const date = new Date(data[d]['date']);

            ispis += 

            `<div class="user_review_container d-flex flex-column flex-sm-row">
                <div class="user">
                    <div class="user_pic"><img class="user_pic" src="${data[d]['profile_picture']}"/></div>
                    <div class="user_rating">
                        <ul class="star_rating">`
                        for (let i = 1; i <= 5; i++) {
                            if (i <= data[d]['rating']) {
                                ispis += `<li><i class="fa fa-star" aria-hidden="true"></i></li> `;
                            } else {
                                ispis += `<li><i class="fa fa-star-o" aria-hidden="true"></i></li> `;
                            }
                        }
                       ispis+= `</ul>
                    </div>
                </div>
                <div class="review">
                    <div class='review_header'>
                        <div class="review_date">${date.getDate()} ${getMonthName(date.getMonth()).slice(0,3)} ${date.getFullYear()}</div>
                        ${userId == data[d]['id_user'] ? `<div class='deleteComment'><button class="btn-remove-comment" data-id="${data[d]['id_comment']}"><i class="fas fa-times"></i></button></div>` : ''}
                    </div>
                    <div class="user_name">${data[d]['first_name']} ${data[d]['last_name']}</div>
                    <div class="review_content"><p>${data[d]['content']}</p></div>
                </div>
            </div>`
        }

        ispis+= `<div class='pagination' id="pagination_reviews"></div>`
        
        return ispis;
    }

    function generateStars(avg) {
        let full = Math.floor(avg);
        let half = (avg - full) >= 0.5;
        let html = "";

        for (let i = 1; i <= 5; i++) {
            if (i <= full) {
                html += `<li><i class="fa-solid fa-star"></i></li>`;
            } else if (half) {
                html += `<li><i class="fa-solid fa-star-half-stroke"></i></li>`;
                half = false;
            } else {
                html += `<li><i class="fa-regular fa-star"></i></li>`;
            }
        }
        return html;
    }


    function deleteProductComment() {
        document.addEventListener('click', function(e) {
            if (e.target.closest('.btn-remove-comment')) {
                const button = e.target.closest('.btn-remove-comment');
                const commentId = button.getAttribute('data-id');

                showConfirmModal("Do you want to remove the comment?", function(confirmed) {
                    if (!confirmed) return;

                    $('#loading-overlay').addClass('show');

                    const ajaxStartTime = performance.now();


                    $.ajax({
                        url: 'models/deleteProductReview.php',
                        method: 'POST',
                        data: {
                            commentId: commentId
                        },
                        dataType: 'json',
                        success: function (response) {
                            const ajaxDuration = performance.now() - ajaxStartTime;
                            const totalDuration = ajaxDuration + 1500;

                            setTimeout(function(){
                                $('#loading-overlay').removeClass('show');

                                if (response.success) {
                                    showToast('Comment removed successfully!', 'success');
                                    getProductComments();
                                } else {
                                    showToast('Failed to remove comment.', 'error');
                                }
                            }, totalDuration)
                            
                        },
                        error: function () {
                            $('#loading-overlay').removeClass('show');
                            showToast('Error removing comment.', 'error');
                        }
                    });
                });
            }
        });
    }


    deleteProductComment();

    function getProductComments(page = currentPage) {

        let productId = parseInt(new URLSearchParams(window.location.search).get('id'));

        if(!productId) return;

        $.ajax({
            url: 'models/getProductReviews.php',
            method: 'POST',
            data: {
                productId: productId,
                page: page
            },
            dataType: 'json',
            success: function(response) {
                const reviewsTab = document.querySelector('.tab[data-active-tab="tab_3"] span');
                const reviewsContainer = $('.reviews_col');
                reviewsContainer.html('');

                if (reviewsTab) {
                    if (response.reviewCount > 0) {
                        reviewsTab.textContent = `Reviews (${response.reviewCount})`;
                    } else {
                        reviewsTab.textContent = `Reviews`;
                    }
                }

                const starsContainer = document.querySelector("#product_main_rating");
                if (starsContainer) {
                    starsContainer.innerHTML = generateStars(response.averageRating);
                }

                if(response.productReviews.length > 0){
                    reviewsContainer.html(reviewWrite(response.productReviews, response.reviewCount));
                    renderPagination(response.totalPages, response.currentPage, '#pagination_reviews');
                    currentPage = response.currentPage;
                } else {
                    reviewsContainer.html(`
                        <div class="tab_title reviews_title">
                                <h4>Reviews</h4>
                            </div>
                        <div>
                            <h3>No reviews!</h3>
                            <p>Currently, there are no reviews for this product!</p>
                        </div>
                    `);
                    $('#pagination_reviews').html('');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', error);
                console.log(xhr.responseText);
            }
        });
    }

    function resetStarsToOne() {
        const stars = document.querySelectorAll('#ratingList li i');
        stars.forEach((star, index) =>
            star.className = `fa ${index === 0 ? 'fa-star' : 'fa-star-o'}`
        );
    }

    function insertProductReview(){
        $('#single_review_submit').on('click', function (e) {
            if (!userId) {
                window.location.href = "index.php?page=login";
                return;
            }

            e.preventDefault();
    
            $('.form_input, textarea').removeClass('error');
            $('.error-message').text('');
    
            const allMessages = document.querySelectorAll('.error-message');
            allMessages.forEach(element => {
                element.style.display = "none"; 
            });
    
            $('#loading-overlay').addClass('show');
    
            const productId = parseInt(new URLSearchParams(window.location.search).get('id'));
            const name = $('#input_name').val().trim();
            const email = $('#input_email').val().trim();
            const rating = document.querySelectorAll('#ratingList .fa-star').length;
            const review = $('#input_review').val().trim();
            
            const errors = [];

            if(!name)
                {
                    errors.push({
                        inputSelector: '#input_name',
                        errorSelector: '#error_name',
                        errorMessage: messageText('fieldCanNotBeEmpty')
                    });
                }
    
                if(!email){
                    errors.push({
                        inputSelector: '#input_email',
                        errorSelector: '#error_email',
                        errorMessage: messageText('fieldCanNotBeEmpty')
                    });
                }

                if(!review){
                    errors.push({
                        inputSelector: '#input_review',
                        errorSelector: '#error_review',
                        errorMessage: messageText('fieldCanNotBeEmpty')
                    });
                }
    
                if(errors.length > 0){
    
                    setTimeout(function () {
    
                        displayErrors(errors);
    
                        $('#loading-overlay').removeClass('show');
    
                    }, 1500);
    
                    return;
                }
    
            const ajaxStartTime = performance.now();
    
            $.ajax({
                url: "models/insertProductReview.php",
                type: "POST",
                data: {
                    name: name,
                    email: email,
                    rating: rating,
                    review: review,
                    productId : productId
                },
                dataType: "json",
                success: function (response) {
                    const ajaxDuration = performance.now() - ajaxStartTime;
                    const totalDuration = ajaxDuration + 1500;
    
                    setTimeout(function () {
                        $('#loading-overlay').removeClass('show');
    
                        if (response.success) 
                        {
                            showToast(response.message, 'success');
                            $('form')[0].reset();
                            resetStarsToOne();
                            getProductComments();
                        }
                        else
                        {
                            displayErrors(response.errors);
                            showToast(response.message, 'error');
                        }
                    }, totalDuration);
                },
                error: function (xhr, status, error) {
                    console.error("Error:", error);
                    console.error("Response Text:", xhr.responseText);
    
                    const ajaxDuration = performance.now() - ajaxStartTime;
                    const totalDuration = ajaxDuration + 1500;
    
                    setTimeout(function () {
                        // showToast("An error occurred. Please try again later.", 'error');
                        $('#loading-overlay').removeClass('show');
                    }, totalDuration);
                }
            });
        });
    }

    let cart = [];
    let totalPages = 1;
    let currentCartPage = 1;

    function syncCartToDatabase() {
        const cart = JSON.parse(localStorage.getItem('cart'));
        const id_user = userId;

        if (!userId) {
            // window.location.href = "index.php?page=login";
            return;
        }
        if (!cart || cart.length === 0) return;

        $.ajax({
            url: 'models/syncProductsCart.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                id_user: id_user,
                cart: cart
            }),
            success: function(response) {
                // console.log("Cart synced:", response);
                if (response.success) {
                    localStorage.removeItem('cart');
                } else {
                    console.error("Server reported error:", response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error("Sync failed:", status, error);
                console.error("Full error:", xhr.responseText);
            }
        });
    }

    $('#product_size').on('focus', function () {
        $(this).removeClass('error');
        $('#error_size').hide();
    });

    $('.selected-color').on('click', function (event) {
        event.stopPropagation();

        $(this).removeClass('error');
        $('#error_color').hide();

        $('.color-options').toggle();
        $(this).toggleClass('active');
    });

    $('.color-options li').on('click', function () {
        const selectedColor = $(this).data('color');
        const colorBoxColor = $(this).find('.color-box').css('background-color');

        $('#selected-box').css('background-color', colorBoxColor).show();
        $('.selected-name').text(selectedColor);

        $('#product_color').val(selectedColor);

        $('.color-options').hide();

    });

    $(document).on('click', function(event) {
        if (!$(event.target).closest('.custom-color-dropdown').length) {
            $('.color-options').hide();
            $('.selected-color').removeClass('active');
        }
    });

    function renderCartItems() {
        const currentPage = url.searchParams.get("page");
        if (currentPage !== 'cart') return;

        const cartContainer = document.querySelector('.cart-items');
        const orderSummary = document.querySelector('.order-summary');
        const cartBody = document.querySelector('.cart-body');
        const itemsPerPage = 2;

        function continueRendering(fetchedCart) {

            cart = fetchedCart;
            totalPages = Math.ceil(cart.length / itemsPerPage);

            if (currentCartPage > totalPages) {
                currentCartPage = totalPages || 1;
            }

            const start = (currentCartPage - 1) * itemsPerPage;
            const end = start + itemsPerPage;
            const paginatedCart = cart.slice(start, end);

            let totalQuantity = 0;

            cart.forEach(element => {
                totalQuantity += element.quantity;
            });

            let itemsHTML = `
                <div class="cart-header">
                    <h2>Your Cart</h2>
                    <p class="item-count">${totalQuantity} ${totalQuantity === 1 ? 'item' : 'items'}</p>
                </div>
            `;

            if (!cart.length) {
                itemsHTML += `
                    <div class="empty-cart">
                        <i class="fas fa-shopping-cart cart-icon"></i>
                        <p class="empty-text">Your cart is currently empty.</p>
                        <a href="index.php?page=home" class="continue-shopping-btn">Continue Shopping</a>
                    </div>
                `;
                orderSummary.style.display = "none";
                cartContainer.innerHTML = itemsHTML;
                cartContainer.style.height = "330px";
                cartBody.classList.add('cartBodyEmpty');
                return;
            }

            paginatedCart.forEach(item => {

            const originalPrice = Number(item.originalPrice) || 0;
            const discountedPrice = Number(item.discountedPrice) || 0;

                itemsHTML += `
                    <div class="cart-item">
                        <div class="item-image">
                            <a href='index.php?page=single&id=${item.id}'><img src="${item.image}" alt="${item.name}"></a>
                        </div>
                        <div class="item-details">
                            <p class="item-type">${item.category}</p>
                            <p class="item-name">${item.name}</p>
                            <p class="item-type">Size: ${item.size}</p>
                            <p class="item-type">Color: ${item.color}</p>
                            <p class="item-variant">Quantity: ${item.quantity}</p>
                        </div>
                        <div class="item-price">
                            ${item.discount > 0 ? `<span class="original-price">$${parseFloat(originalPrice).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span>` : ''}
                            <span class="current-price">$${parseFloat(discountedPrice).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span>
                        </div>
                        <div class="item-quantity">
                            <button class="qty-btn btn-decrease" ${item.quantity == 1 ? "aria-disabled='true'" : ''} data-id="${item.id}" data-size="${item.size}" data-color="${item.color}">-</button>
                            <span class="qty-value">${item.quantity}</span>
                            <button class="qty-btn btn-increase" data-id="${item.id}" data-size="${item.size}" data-color="${item.color}">+</button>
                        </div>
                        <div class="item-remove">
                            <button class="btn-remove" data-id="${item.id}" data-size="${item.size}" data-color="${item.color}"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                `;
            });

            itemsHTML += `
                <div class="cart-bottom-controls">
                    <div class="cart-pagination">
                        <a class="pagination-btn prev-btn" ${currentCartPage === 1 ? "aria-disabled='true'" : ''}>
                            <i class="fas fa-chevron-left"></i> Previous
                        </a>
                        <span class="page-indicator">Page ${currentCartPage} of ${totalPages}</span>
                        <a class="pagination-btn next-btn" ${currentCartPage === totalPages ? "aria-disabled='true'" : ''}>
                            Next <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                    <a href='index.php?page=home' class="btn-back-to-shop">
                        <i class="fas fa-arrow-left"></i> Back to Shop
                    </a>
                </div>
            `;

            cartContainer.innerHTML = itemsHTML;
            renderOrderSummary(cart);
            updateCartCounter();
        }

        if (userId) {
            $.ajax({
                url: "models/getProductsCart.php",
                method: "GET",
                dataType: "json",
                success: function(response) {
                    continueRendering(response.products || []);
                },
                error: function(xhr, status, error) {
                    console.error("Error:", error);
                    console.error("Response Text:", xhr.responseText);
                }
            });
        } 
        else 
        {
            const localCart = JSON.parse(localStorage.getItem('cart')) || [];

            $.ajax({
                url: "models/setGuestCart.php",
                method: "POST",
                contentType: "application/json",
                dataType: "json", 
                data: JSON.stringify({ cart: localCart }),
                success: function (response) {
                    if (response.success) {
                        continueRendering(localCart);
                    } 
                },
                error: function (xhr, status, error) {
                    console.error("Error:", error);
                    console.error("Response Text:", xhr.responseText);
                }
            });
        }
    }

    function renderCheckout() {
        const currentPage = url.searchParams.get("page");
        if (currentPage !== 'checkout') return;

        function continueRendering(fetchedCart) {

            cart = fetchedCart;
            
            renderOrderSummary(cart);
        }

        if (userId) {
            $.ajax({
                url: "models/getProductsCart.php",
                method: "GET",
                dataType: "json",
                success: function(response) {
                    continueRendering(response.products || []);
                },
                error: function(xhr, status, error) {
                    console.error("Error:", error);
                    console.error("Response Text:", xhr.responseText);
                }
            });
        } 
        else 
        {
            const localCart = JSON.parse(localStorage.getItem('cart')) || [];

            $.ajax({
                url: "models/setGuestCart.php",
                method: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({ cart: localCart }),
                success: function (response) {
                    if (response.success) {
                        continueRendering(localCart);
                    } 
                },
                error: function (xhr, status, error) {
                    console.error("Error:", error);
                    console.error("Response Text:", xhr.responseText);
                }
            });
        }
    }

    document.addEventListener("click", function (e) {
        if (e.target.closest(".prev-btn") && currentCartPage > 1) {
            currentCartPage--;
            renderCartItems();
            renderOrderItems();
        }

        if (e.target.closest(".next-btn") && currentCartPage < totalPages) {
            currentCartPage++;
            renderCartItems();
            renderOrderItems();
        }
    });


    const shippingOptions = document.querySelectorAll('input[name="shipping"]');

    if (shippingOptions.length > 0) {
        shippingOptions.forEach(option => {
            option.addEventListener('change', () => {
                const selectedLabel = option.closest('.radio-option');
                const name = selectedLabel.querySelector('.radio-title')?.textContent.trim();
                const priceText = selectedLabel.querySelector('.radio-price')?.textContent.trim();
                const price = priceText === 'Free' ? 0 : parseFloat(priceText.replace('$', ''));

                renderOrderSummary(cart, { name, price });
            });
        });
    }

    function renderOrderSummary(cart, shipping = { name: "Standard", price: 0 }) {
        let subtotal = 0;

        cart.forEach(item => {
            subtotal += item.discountedPrice * item.quantity;
        });

        // const tax = subtotal * 0.065;
        // const total = subtotal + tax + shipping.price;

        const total = subtotal + shipping.price;

        const currentPage = url.searchParams.get("page");

        // let summaryHTML = `
        //     <div class="total-row">
        //         <span>Subtotal (${cart.length} ${cart.length === 1 ? 'item' : 'items'})</span>
        //         <span>$${subtotal.toFixed(2)}</span>
        //     </div>
        //     <div class="total-row">
        //         <span>Tax</span>
        //         <span>$${tax.toFixed(2)}</span>
        //     </div>
        // `;

        let totalQuantity = 0;

        cart.forEach(element => {
            totalQuantity += element.quantity;
        });

        let summaryHTML = `
            <div class="total-row">
                <span>Subtotal (${totalQuantity} ${totalQuantity === 1 ? 'item' : 'items'})</span>
                <span>$${parseFloat(subtotal).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span>
            </div>
        `;

        if (currentPage === 'checkout') {
            summaryHTML += `
                <div class="total-row">
                    <span>Shipping</span>
                    <span>${shipping.price > 0 ? `$${parseFloat(shipping.price).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}` : 'Free'} (${shipping.name} Shipping) </span>
                </div>
            `;
        }

        summaryHTML += `
            <div class="total-row grand-total">
                <span>Total</span>
                <span>$${parseFloat(total).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span>
            </div>
        `;

        document.querySelector('.summary-totals').innerHTML = summaryHTML;
    }

    function addProductToCart(){
        $(document).on('click', '.add-cart-btn', function(e){
            e.preventDefault();

            $('.error-message').text('');

            const allMessages = document.querySelectorAll('.error-message');
                allMessages.forEach(element => {
                element.style.display = "none"; 
            });

            const errors = [];

            const size = $('#product_size').val();
            const color = $('#product_color').val();
            const button = $(this);

            if(!size){
                errors.push({
                    inputSelector: '#product_size',
                    errorSelector: '#error_size',
                    errorMessage: messageText('pleaseSelectAnOption')
                })
            }

            if(!color){
                errors.push({
                    inputSelector: '.selected-color',
                    errorSelector: '#error_color',
                    errorMessage: messageText('pleaseSelectAnOption')
                })
            }

            if(errors.length > 0){

                displayErrors(errors);

                return;
            }
            

            const id = $(this).data('id');
            const name = $(this).data('name');
            const originalPrice = parseFloat($(this).data('originalPrice'));
            const discountedPrice = parseFloat($(this).data('discountedPrice'));
            const discount = parseFloat($(this).data('discount'));
            const image = $(this).data('image');
            const category = $(this).data('category');

            if(userId){
                $.ajax({
                    url: 'models/insertProductToCart.php',
                    method: 'POST',
                    data: {
                        id_product: id,
                        quantity: 1,
                        id_size : size,
                        id_color : color
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            showToast('Product added successfully!', 'success');

                            updateCartCounter();

                        } else {
                            showToast('Failed to added product to cart.', 'error');
                        }
                    },
                    error: function () {
                        showToast('Error adding product to cart.', 'error');
                    }
                });
            }
            else{
                let cart = JSON.parse(localStorage.getItem('cart')) || [];

                const existingItem = cart.find(item =>
                    item.id == id &&
                    item.size == size &&
                    item.color == color
                );

                if (existingItem) {
                    existingItem.quantity += 1;
                } else {
                    cart.push({ id, name, originalPrice, discountedPrice, discount, image, category, size, color, quantity: 1 });
                }

                localStorage.setItem('cart', JSON.stringify(cart));

                showToast('Product successfully added to cart!', 'success');

                updateCartCounter();
            }
        })
    }


    function deleteProductFromCart2(){
        $(document).on('click', '.btn-remove', function () {
            const id = $(this).data('id');
            const size = $(this).data('size');
            const color = $(this).data('color');

            showConfirmModal("Do you want to remove product from cart?", function(confirmed) {
                if (!confirmed) return;

                if(userId){
                    $.ajax({
                        url: 'models/deleteProductFromCart.php',
                        method: 'POST',
                        data: {
                            id_product: id,
                            size: size,
                            color: color
                        },
                        dataType: 'json',
                        success: function (response) {
                            if (response.success) {
                                showToast('Product removed successfully!', 'success');
                                renderCartItems();
                                updateCartCounter();
                            } else {
                                showToast('Failed to remove product from cart.', 'error');
                            }
                        },
                        error: function () {
                            showToast('Error removing product from cart.', 'error');
                        }
                    });
                } else {
                    let cart = JSON.parse(localStorage.getItem('cart')) || [];

                    cart = cart.filter(item =>
                        !(item.id == id && item.size == size && item.color == color)
                    );

                    localStorage.setItem('cart', JSON.stringify(cart));
                    renderCartItems();
                    updateCartCounter();

                    showToast('Product removed successfully!', 'success');
                }
            });
        });
    }


    function handleIncreaseQuantity(){
        $(document).on('click', '.btn-increase', function () {
            const id = $(this).data('id');
            const size = $(this).data('size');
            const color = $(this).data('color');

            if(userId){
                $.ajax({
                    url: 'models/updateProductFromCart.php',
                    method: 'POST',
                    data: {
                        id_product: id,
                        size: size,
                        color: color,
                        type: 'increase'
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            renderCartItems();
                        }
                    },
                    error: function () {
                        console.error("Error updating cart.");
                    }
                });
            } else {
                let cart = JSON.parse(localStorage.getItem('cart')) || [];

                cart.forEach(item => {
                    if (item.id == id && item.size == size && item.color == color) {
                        item.quantity++;
                    }
                });

                localStorage.setItem('cart', JSON.stringify(cart));
                renderCartItems();
            }
        });
    }

    
    function handleDecreaseQuantity(){
        $(document).on('click', '.btn-decrease', function () {
            const id = $(this).data('id');
            const size = $(this).data('size');
            const color = $(this).data('color');

            if(userId){
                $.ajax({
                    url: 'models/updateProductFromCart.php',
                    method: 'POST',
                    data: {
                        id_product: id,
                        size: size,
                        color: color,
                        type: 'decrease'
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            renderCartItems();
                        }
                    },
                    error: function () {
                        console.error("Error updating product quantity.");
                    }
                });
            }
            else{
                let cart = JSON.parse(localStorage.getItem('cart')) || [];

                cart.forEach(item => {
                    if (item.id == id && item.size == size && item.color == color && item.quantity > 1) {
                        item.quantity--;
                    }
                });

                localStorage.setItem('cart', JSON.stringify(cart));
                renderCartItems();
            }
        });
    }
    

    function updateCartCounter() {
        function continueUpdatingCounter(fetchedCart){

            const counterCart = fetchedCart;
            const counter = document.getElementById('checkout_items');

            let totalQuantity = 0;

            counterCart.forEach(element => {
                totalQuantity += element.quantity;
            });

            if (totalQuantity > 0) {
                counter.hidden = false;
                counter.textContent = totalQuantity;
            } else {
                counter.hidden = true;
                counter.textContent = 0;
            }
        }

        if (userId) {
            $.ajax({
                url: "models/getProductsCart.php",
                method: "GET",
                dataType: "json",
                success: function(response) {
                    continueUpdatingCounter(response.products || []);
                },
                error: function(xhr, status, error) {
                    console.error("Error:", error);
                    console.error("Response Text:", xhr.responseText);
                }
            });
        } 
        else 
        {
            const localCart = JSON.parse(localStorage.getItem('cart')) || [];

            $.ajax({
                url: "models/setGuestCart.php",
                method: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({ cart: localCart }),
                success: function (response) {
                    if (response.success) {
                        continueUpdatingCounter(localCart);
                    } 
                },
                error: function (xhr, status, error) {
                    console.error("Error:", error);
                    console.error("Response Text:", xhr.responseText);
                }
            });
        }

    }

    if(url.searchParams.get("page") == 'checkout'){

        document.getElementById('country').addEventListener('change', function () {
            const countryId = this.value;
            const citySelect = document.getElementById('city');
            const zipInput = document.getElementById('zip');

            citySelect.innerHTML = '<option value="">Select city</option>';
            citySelect.disabled = true;

            zipInput.value = '';
            zipInput.disabled = true;

            if (countryId) {
                fetch('models/getCities.php?country_id=' + countryId)
                    .then(response => response.json())
                    .then(cities => {
                        cities.forEach(city => {
                            const option = document.createElement('option');
                            option.value = city.id_city;
                            option.textContent = city.name;
                            option.setAttribute('data-zip', city.zip_code);
                            citySelect.appendChild(option);
                        });
                        citySelect.disabled = false;
                    });
            }
        });
    

        document.getElementById('city').addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const zip = selectedOption.getAttribute('data-zip');
            const zipInput = document.getElementById('zip');

            if (zip) {
                zipInput.value = zip;
                zipInput.disabled = false;
            } else {
                zipInput.value = '';
                zipInput.disabled = true;
            }
        });
    }

    // function getCartTotal() {
    //     if (!cart || cart.length === 0) return "0.00";

    //     const selectedShippingRadio = document.querySelector('input[name="shipping"]:checked');
    //     let shippingPrice = 0;
    //     if (selectedShippingRadio) {
    //         shippingPrice = parseFloat(selectedShippingRadio.dataset.price || 0);
    //     }

    //     let subtotal = 0;
    //     cart.forEach(item => {
    //         subtotal += item.discountedPrice * item.quantity;
    //     });

    //     const total = subtotal + shippingPrice;

    //     return total.toFixed(2);
    // }

    function insertOrder() {
        $('#insert_order').on('click', function (e) {
            e.preventDefault();

            // const cart = JSON.parse(localStorage.getItem('cart'));
    
            $('.form_input, textarea').removeClass('error');
            $('.error-message').text('');

            const allMessages = document.querySelectorAll('.error-message');
            allMessages.forEach(element => {
                element.style.display = "none"; 
            });
    
            $('#loading-overlay').addClass('show');
    
            const email = $('#email').val().trim();
            const firstName = $('#first_name').val().trim();
            const lastName = $('#last_name').val().trim();
            const streetName = $('#street_name').val().trim();
            const streetNumber = $('#street_number').val().trim();
            const country = $('#country').val().trim();
            const city = $('#city').val().trim();
            const phoneNumber = $('#phone_number').val().trim();
            const selectedShipping = $('input[name="shipping"]:checked').val();

            const ajaxStartTime = performance.now();
    
            $.ajax({
                url: "models/insertOrder.php",
                type: "POST",
                contentType: "application/json",
                data: JSON.stringify({
                    email: email,
                    firstName: firstName,
                    lastName: lastName,
                    streetName: streetName,
                    streetNumber: streetNumber,
                    country: country,
                    city: city,
                    phoneNumber: phoneNumber,
                    selectedShipping: selectedShipping
                }),
                dataType: "json",
                success: function (response) {
                    const ajaxDuration = performance.now() - ajaxStartTime;
                    const totalDuration = ajaxDuration + 1500;
    
                    setTimeout(function () {
                        $('#loading-overlay').removeClass('show');
    
                        if (response.success) 
                        {
                            openPaymentModal();
                        }
                        else
                        {
                            displayErrors(response.errors);
                            showToast(response.message, 'error');
                        }
                    }, totalDuration);
                },
                error: function (xhr, status, error) {
                    console.error("Error:", error);
                    console.error("Response Text:", xhr.responseText);
    
                    const ajaxDuration = performance.now() - ajaxStartTime;
                    const totalDuration = ajaxDuration + 1500;
    
                    setTimeout(function () {
                        // showToast("An error occurred. Please try again later.", 'error');
                        $('#loading-overlay').removeClass('show');
                    }, totalDuration);
                }
            });
        });
    }
    
    insertOrder();

    function openPaymentModal() {
        const modal = document.getElementById("payment-modal");
        modal.classList.add("active");
        document.body.style.overflow = "hidden";
        renderPayPalButton();
    }

    function closePaymentModal() {
        const modal = document.getElementById("payment-modal");
        modal.classList.remove("active");
        document.body.style.overflow = "auto";
        document.getElementById("paypal-button-container").innerHTML = "";
    }


    $("#close-payment").on("click", closePaymentModal);

    $("#payment-modal").on("click", function (e) {
        if (e.target.id === "payment-modal") {
            closePaymentModal();
        }
    });

    function renderPayPalButton() {

        const container = document.getElementById("paypal-button-container");

        container.innerHTML = "";

        paypal.Buttons({
            createOrder: function(data, actions) {
                return fetch('models/createPayPalOrder.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        cart : cart 
                    })
                })
                .then(res => res.json())
                .then(order => order.id);
            },

            onApprove: function(data, actions) {
                return fetch('models/capturePayment.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ paypalOrderId: data.orderID })
                })
                .then(res => res.json())
                .then(captureData => {
                    if (captureData.success) {
                        closePaymentModal();
                        finalizeOrder(data.orderID, captureData.transactionId);
                    } else {
                        alert('Payment failed. Please try again.');
                    }
                });
            }

        }).render('#paypal-button-container');
    }

    function finalizeOrder(paypalOrderId, transactionId){
        $.ajax({
            url: "models/finalizeOrder.php",
            type: "POST",
            contentType: "application/json",
            data: JSON.stringify({
                paypalOrderId: paypalOrderId,
                transactionId : transactionId,
                cart: cart
            }),
            success: function () {
                localStorage.removeItem('cart');

                localStorage.setItem('orderSuccessMessage', 'Order successfully submited!');

                window.location.href = "index.php?page=home";
            }
        });
    }

    if(url.searchParams.get("page") == 'home'){
        const successMessage = localStorage.getItem('orderSuccessMessage');
        if (successMessage) {
            showToast(successMessage, 'success');
            localStorage.removeItem('orderSuccessMessage'); 
        }
    }

    function orderWrite(data){
        var ispis = '';

        for(let d in data){

            const date = new Date(data[d]['date']);

            ispis += `<div class="product-item">
            <div class="product discount product_filter">`

            ispis += `<div class="product_info">
            
                        <h6 class="product_name order_number"><a href="index.php?page=orderInfo&id=${data[d]['orderId']}">Order number: ${data[d]['orderId']}</a></h6>

                        <h6 class="product_name product_line">Order date: ${date.getDate()} ${getMonthName(date.getMonth()).slice(0,3)} ${date.getFullYear()}</h6>

                        <h6 class="product_name product_line">Order status: <span class="btn btn-sm ${data[d]['statusClass']}" style="font-size: 0.7rem; padding: 0.1rem 0.4rem; pointer-events: none; cursor: default;">${data[d]['orderStatusName']}</span></h6>


                        <h6 class="product_name product_line">Total price: $${parseFloat(data[d]['totalPrice']).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</h6>
                    </div>
                    
                    <div class='ordersButton'>
                        <a class='viewOrderDetails' href='index.php?page=orderInfo&id=${data[d]['orderId']}'>
                            View details
                        </a>
                    </div>

                </div>`

            ispis += `</div>`
        }

        return ispis;
    }

    function getOrders(page = currentPage) {
        const currentUrl = url.searchParams.get("page");
        
        if (!userId || currentUrl !== 'orders') {
            return;
        }

        const id_user = userId;
        
        $.ajax({
            url: 'models/getOrders.php',
            method: 'POST',
            data: {
                id_user : id_user,
                page: page
            },
            dataType: 'json',
            success: function(response) {
                if (response.orders.length > 0) {
                    $("#user-orders").html(orderWrite(response.orders));
                    $('#user-orders').isotope('reloadItems').isotope();
    
                    renderPagination(response.totalPages, response.currentPage, '#pagination_orders');
    
                    $('#showing_results_orders').html(
                        `Showing page ${response.currentPage} of ${response.totalPages} (${response.totalOrders} ${response.totalOrders === 1 ? 'order' : 'orders'})`
                    );
    
                    currentPage = response.currentPage;
                }
                else if (page !== 1) {
                    getOrders(1);
                }
                else {
                    const emptyOrdersHTML = `
                        <div class="empty-wishlist">
                            <h3>You currently don't have any orders!</h3>
                            <p>You haven't submitted any orders yet. Start exploring our collection and order now.</p>
                            <a href="index.php?page=home" ><button class="browse-btn">Browse Products</button></a>
                        </div>
                    `;
                    $("#user-orders").html(emptyOrdersHTML);
                    $('#pagination_orders').empty();
                    $('#showing_results_orders').empty();
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', error);
                console.log(xhr.responseText);
            }
        });
    }

    function renderOrderItems() {
        const currentPage = url.searchParams.get("page");
        const orderId = url.searchParams.get("id");

        if (currentPage !== 'orderInfo') return;

        const cartContainer = document.querySelector('.order-items');
        const itemsPerPage = 2;

        function continueRendering(fetchedCart) {

            cart = fetchedCart;
            totalPages = Math.ceil(cart.length / itemsPerPage);

            if (currentCartPage > totalPages) {
                currentCartPage = totalPages || 1;
            }

            const start = (currentCartPage - 1) * itemsPerPage;
            const end = start + itemsPerPage;
            const paginatedCart = cart.slice(start, end);

            let totalQuantity = 0;

            cart.forEach(element => {
                totalQuantity += element.quantity;
            });

            let itemsHTML = `
                <div class="order-header">
                    <h2>Order items</h2>
                    <p class="item-count">${totalQuantity} ${totalQuantity === 1 ? 'item' : 'items'}</p>
                </div>
            `;

            paginatedCart.forEach(item => {

            const originalPrice = Number(item.originalPrice) || 0;
            const discountedPrice = Number(item.discountedPrice) || 0;

                itemsHTML += `
                    <div class="order-item">
                        <div class="item-image">
                            <a href='index.php?page=single&id=${item.id_product}'><img src="${item.cover_photo}" alt="${item.name}"></a>
                        </div>
                        <div class="item-details">
                            <p class="item-type">${item.category}</p>
                            <p class="item-name">${item.name}</p>
                            <p class="item-type">Size: ${item.size}</p>
                            <p class="item-type">Color: ${item.color}</p>
                            <p class="item-variant">Quantity: ${item.quantity}</p>
                        </div>
                        <div class="item-price">
                            ${item.discount > 0 ? `<span class="original-price">$${parseFloat(originalPrice).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span>` : ''}
                            <span class="current-price">$${parseFloat(discountedPrice).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span>
                        </div>
                    </div>
                `;
            });

            itemsHTML += `
                <div class="order-bottom-controls">
                    <div class="order-pagination">
                        <a class="pagination-btn prev-btn" ${currentCartPage === 1 ? "aria-disabled='true'" : ''}>
                            <i class="fas fa-chevron-left"></i> Previous
                        </a>
                        <span class="page-indicator">Page ${currentCartPage} of ${totalPages}</span>
                        <a class="pagination-btn next-btn" ${currentCartPage === totalPages ? "aria-disabled='true'" : ''}>
                            Next <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                </div>
            `;

            cartContainer.innerHTML = itemsHTML;
        }


        $.ajax({
            url: "models/getOrderItems.php",
            method: "POST",
            data : {
                orderId : orderId
            },
            dataType: "json",
            success: function(response) {
                continueRendering(response.orderItems || []);
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', error);
                console.log(xhr.responseText);
            }
        });
    }

    renderOrderItems();


    function newsletterSubscription() {
        $('#newsletter_submit').on('click', function (e) {
            e.preventDefault();
    
            $('.form_input, textarea').removeClass('error');
            $('.error-message').text('');

            const allMessages = document.querySelectorAll('.error-message');
            allMessages.forEach(element => {
                element.style.display = "none"; 
            });
    
            $('#loading-overlay').addClass('show');

            const email = $('#newsletter_email').val().trim();
    
            const ajaxStartTime = performance.now();
    
            $.ajax({
                url: "models/insertNewsletterSubscription.php",
                type: "POST",
                data: {
                    email: email
                },
                dataType: "json",
                success: function (response) {
                    const ajaxDuration = performance.now() - ajaxStartTime;
                    const totalDuration = ajaxDuration + 1500;
    
                    setTimeout(function () {
                        $('#loading-overlay').removeClass('show');
    
                        if (response.success) 
                        {
                            showToast(response.message, 'success');
                            $('form')[0].reset();
                            // toggleButton.style.display = "none";
                        }
                        else
                        {
                            displayErrors(response.errors);
                            showToast(response.message, 'error');
                        }
                    }, totalDuration);
                },
                error: function (xhr, status, error) {
                    console.error("Error:", error);
                    console.error("Response Text:", xhr.responseText);
    
                    const ajaxDuration = performance.now() - ajaxStartTime;
                    const totalDuration = ajaxDuration + 1500;
    
                    setTimeout(function () {
                        // showToast("An error occurred. Please try again later.", 'error');
                        $('#loading-overlay').removeClass('show');
                    }, totalDuration);
                }
            });
        });
    }
    
    newsletterSubscription();


    const urlParams = new URLSearchParams(window.location.search);
    const rawMessage = urlParams.get('message');

    if (rawMessage) {
        try {
            const messageObj = JSON.parse(decodeURIComponent(rawMessage));

            if (messageObj.success) {
                showToast(messageObj.success, 'success');
            } else if (messageObj.error) {
                showToast(messageObj.error, 'error');
            }

            window.history.replaceState({}, document.title, window.location.pathname + window.location.search.replace(/(&|\?)message=[^&]*/, ''));
        } catch (e) {
            console.error('Failed to parse message:', e);
        }
    }

    getUserInformation()
        .then(() => {
            populateUserForm();
            insertProductReview();
            syncCartToDatabase();
            getWishlistProducts(currentPage);
            getProductComments(currentPage);
            renderCartItems();
            updateCartCounter();
            deleteProductFromCart2();
            addProductToCart();
            handleIncreaseQuantity();
            handleDecreaseQuantity();
            renderCheckout();
            getOrders(currentPage);
            updateUserInformation();
            removeProfilePicture();
        })
        .catch(() => {
            console.warn("User not logged in or error occurred.");
            populateUserForm();
            insertProductReview();
            syncCartToDatabase();
            getWishlistProducts(currentPage);
            getProductComments(currentPage);
            renderCartItems();
            updateCartCounter();
            deleteProductFromCart2();
            addProductToCart();
            handleIncreaseQuantity();
            handleDecreaseQuantity();
            renderCheckout();
            getOrders(currentPage);
            updateUserInformation();
            removeProfilePicture();
        });

    //////////////////////////////////////////////////////////////////
});