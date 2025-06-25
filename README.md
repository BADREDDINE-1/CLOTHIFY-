# 👕 CLOTHIFY

CLOTHIFY is a modern and responsive **PHP & MySQL-based e-commerce website** built for clothing stores. It includes user authentication, product management, shopping cart functionality, and a professional admin dashboard.

## 🛍️ Features

### ✅ Frontend (Customer Side)
- 🧾 **User Registration & Login**
- 📧 Email Verification (with PHPMailer)
- 🔒 Password Reset with Token System
- 🛍️ Product Browsing
- ➕ Add to Cart / Remove from Cart
- 💳 Checkout Page (styled payment form)
- 👤 User Profile Page
- 🧱 Responsive Design with modern UI (HTML, CSS, JS)

### 🛠️ Backend (Admin Side)
- 🛂 Admin Authentication & Role Check
- 📦 Add / Edit / Delete Products
- 📁 Image Uploads with Preview
- 📊 Dashboard for Product Management
- 🔐 Session-Based Access Control

### 💡 Technologies Used
- **PHP (OOP + Procedural)**
- **MySQL** (PDO for secure DB interactions)
- **HTML5 / CSS3 / JavaScript**
- **Bootstrap** for responsive layout
- **Font Awesome** for icons
- **PHPMailer** for sending verification & reset emails
- **FileZilla** for deployment
- **GitHub** for version control

## 📂 Project Structure

<pre markdown="1"> ```markdown CLOTHIFY/
├── index.php               # Homepage
├── register.php            # Registration form
├── login.php               # Login form
├── profile.php             # User profile page
├── payment.php             # Checkout/payment form
├── cart.php                # Shopping cart
├── admin/
│   ├── dashboard.php       # Admin dashboard
│   ├── add_product.php     # Add new product
│   ├── edit_product.php    # Edit product
│   └── all_product.php     # View/delete products
├── includes/
│   ├── header.php
│   ├── footer.php
│   └── db.php              # DB connection (PDO)
├── css/
│   └── style.css           # Shared styling
├── images/                 # Product images
├── js/                     # Optional JavaScript
└── README.md               # Project info
 ``` </pre>
