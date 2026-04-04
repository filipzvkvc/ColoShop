$(document).ready(function () {

  const params = new URLSearchParams(window.location.search);
  let currentPage = params.get("page") || "dashboard";

  const links = document.querySelectorAll(".nav-link");

  links.forEach(link => {
    const href = link.getAttribute("href");
    if (!href || !href.includes("page=")) return;

    const linkParams = new URLSearchParams(href.split('?')[1]);
    const linkPage = linkParams.get("page");

    if (linkPage === currentPage) {
      link.classList.add("active");

      const treeview = link.closest(".nav-treeview");
      if (treeview) {
        const parentLi = treeview.closest(".nav-item");
        if (parentLi) {
          parentLi.classList.add("menu-open");
          const parentLink = parentLi.querySelector(".nav-link");
          if (parentLink) {
            parentLink.classList.add("active");
          }
        }
      }
    }
  });

  const priceInput = document.getElementById("price");

  if (priceInput) {
    priceInput.addEventListener("blur", function () {
      let value = priceInput.value.trim();

      if (!value) return;

      value = parseFloat(value.replace(',', '.'));

      if (!isNaN(value)) {
        priceInput.value = value.toFixed(2);
      }
    });
  }

});


function showToast(message, type = 'success') {
        const container = document.querySelector('.my-toast-container') || createToastContainer();
        const toast = document.createElement('div');
        toast.className = `my-toast ${type}`;
    
        const content = document.createElement('span');
        content.textContent = message;
    
        const closeButton = document.createElement('button');
        closeButton.textContent = '×';
        closeButton.className = 'my-toast-close';
        closeButton.onclick = () => slideOutToast(toast);
    
        const progressBar = document.createElement('div');
        progressBar.className = 'my-toast-progress';
    
        toast.appendChild(closeButton);
        toast.appendChild(content);
        toast.appendChild(progressBar);
    
        container.appendChild(toast);
    
        progressBar.style.animationDuration = '8s';
    
        const timeout = setTimeout(() => {
            slideOutToast(toast);
        }, 5000);
    
        closeButton.addEventListener('click', () => clearTimeout(timeout));
    }
    
    function createToastContainer() {
        const container = document.createElement('div');
        container.className = 'my-toast-container';
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

function showConfirmModal(message, callback) {
    const modal = document.getElementById('confirmModal');
    const overlay = document.getElementById('pageOverlay');
    const msg = document.getElementById('confirmMessage');
    const yesBtn = document.getElementById('confirmYes');
    const noBtn = document.getElementById('confirmNo');

    msg.textContent = message;

    overlay.style.display = "block";
    modal.style.display = "flex";

    yesBtn.onclick = function() {
        modal.style.display = "none";
        overlay.style.display = "none";
        callback(true);
    };

    noBtn.onclick = function() {
        modal.style.display = "none";
        overlay.style.display = "none";
        callback(false);
    };
}


function initUniversalDeleteConfirm() {
    const deleteButtons = document.querySelectorAll('.delete-btn');

    const typeMessages = {
        product: "product",
        user: "user",
        order: "order",
        newsletter: "newsletter subscription",
        orderItem: "order item",
        comment: "comment"
    };

    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();

            const type = this.getAttribute('data-type');
            const typeText = typeMessages[type] || type;

            showConfirmModal(`Are you sure you want to delete this ${typeText}?`, function (confirmed) {
                if (confirmed) {
                    let url;
                    if (type === "orderItem") {
                        const orderItemId = btn.getAttribute('data-order-item-id');
                        const orderId = btn.getAttribute('data-order-id');
                        url = `index.php?page=deleteOrderItem&orderItemId=${orderItemId}&orderId=${orderId}`;
                    } else {
                        const id = btn.getAttribute('data-id');
                        url = `index.php?page=delete${capitalize(type)}&id=${id}`;
                    }

                    window.location.href = url;
                }
            });
        });
    });
}

function capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

function initRemovePictureButtons(containerSelector = document) {
    const removeButtons = containerSelector.querySelectorAll('.remove-picture-btn');

    removeButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const type = this.dataset.type;
            const filePath = this.dataset.path;
            const imgSelector = this.dataset.imgSelector;

            fetch("models/removeImage.php", {
                method: "POST",
                headers: {"Content-Type": "application/json"},
                body: JSON.stringify({ type, filePath })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message || "Image removed!", "success");

                    const wrapper = document.querySelector(imgSelector);

                    if (!wrapper) return;

                    if (type === "profile_photo" || type === "profile_photo_temp") {
                        wrapper.src = data.newSrc;
                        if (data.newSrc.includes("default_profile_picture.jpg")) {
                            btn.style.display = "none";
                        }
                    } else if (type === "edit_cover_temp" || type === "edit_cover_real") {
                        const img = wrapper.querySelector('img');
                        const removeBtn = wrapper.querySelector('.remove-picture-btn');
                        
                        if (data.newSrc) {
                            img.src = data.newSrc;
                            
                            if (data.newSrc.includes("default_product_picture.jpg")) {
                                if (removeBtn) removeBtn.remove();
                            } else {
                                if (removeBtn && type === "edit_cover_temp") {
                                    removeBtn.dataset.type = "edit_cover_real";
                                    removeBtn.dataset.path = data.newSrc.replace("../", "");
                                }
                            }
                        }
                    } else {
                        wrapper.remove();
                    }

                } else {
                    showToast(data.message || "Error removing image", "error");
                }
            })
            .catch(() => showToast("Network error", "error"));
        });
    });
}


document.addEventListener("DOMContentLoaded", function() {
    initUniversalDeleteConfirm();
    initRemovePictureButtons();
});