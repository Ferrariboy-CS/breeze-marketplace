# PHP MySQL Marketplace

This is an online marketplace platform built using PHP and MySQL, where users can buy and sell products through separate customer, seller, and admin areas.

![Marketplace Banner](./src/images/banner.png?raw=true)

---

## Features

- User registration and login with role-based access
- Product listings with prices, descriptions, and multiple images
- Category browsing, price-range UI, and wishlist support
- Shopping cart and checkout summary flow
- Admin management for seller and user accounts, including blocking and unblocking

---

## Login Page

![Login Roles](./src/images/roles.png?raw=true)

- Admin login: `Iqbolshoh`
- Seller login: `seller`
- User login: `user`
- Password: `IQBOLSHOH`

---

## User Roles

### Admin

![Admin Panel](./src/images/admin.png?raw=true)

- Manages marketplace accounts from the admin dashboard
- Can view seller and user lists
- Can change account status between active and blocked

### Seller

![Seller Panel](./src/images/seller.png?raw=true)

- Can add products for sale
- Can manage product details and uploaded images
- Has a separate seller dashboard

### User

![User Panel](./src/images/user.png?raw=true)

- Can browse products by category
- Can add items to the cart and wishlist
- Can continue to the checkout summary page

---

## Installation Guide

To set up the PHP MySQL Marketplace, follow these steps.

### 1. Clone the Repository

```bash
git clone https://github.com/Iqbolshoh/php-mysql-marketplace.git
```

### 2. Navigate to the Project Directory

```bash
cd php-mysql-marketplace
```

### 3. Place the Project in Your Web Server Root

Example XAMPP path:

```text
C:\xampp\htdocs\breeze-marketplace
```

### 4. Set Up the Database

- Start Apache and MySQL.
- Import `database.sql` into MySQL.
- The script creates a database named `marketplace` and seeds sample data.

Example:

```bash
mysql -u root -p < database.sql
```

Or from phpMyAdmin:

- Create or select the `marketplace` database.
- Import `database.sql`.

### 5. Configure Database Connection

Open `config.php` in the project root and verify the credentials:

```php
public function __construct()
{
		$servername = "localhost";
		$username = "root";
		$password = "";
		$dbname = "marketplace";

		$this->conn = new mysqli($servername, $username, $password, $dbname);

		if ($this->conn->connect_error) {
				die("Connection failed: " . $this->conn->connect_error);
		}
}
```

### 6. Check the Upload Directory

Make sure this folder exists and is writable by PHP:

```text
src/images/products/
```

### 7. Run the Application

Open the project in your browser:

```text
http://localhost/breeze-marketplace/
```

If you are not authenticated yet, sign in from:

```text
http://localhost/breeze-marketplace/login/
```

---

## Technologies Used

![HTML](https://img.shields.io/badge/HTML-%23E34F26.svg?style=for-the-badge&logo=html5&logoColor=white)
![CSS](https://img.shields.io/badge/CSS-%231572B6.svg?style=for-the-badge&logo=css3&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-%23563D7C.svg?style=for-the-badge&logo=bootstrap&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-%23F7DF1C.svg?style=for-the-badge&logo=javascript&logoColor=black)
![jQuery](https://img.shields.io/badge/jQuery-%230e76a8.svg?style=for-the-badge&logo=jquery&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-%23777BB4.svg?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-%234479A1.svg?style=for-the-badge&logo=mysql&logoColor=white)

## Contributing

Contributions are welcome. If you want to improve the project, fork the repository and open a pull request.

## Connect With Me

You can reach the project author on these platforms:

<div align="center">
	<table>
		<tr>
			<td>
				<a href="https://iqbolshoh.uz" target="_blank">
					<img src="https://img.icons8.com/color/48/domain.png"
							 height="40" width="40" alt="Website" title="Website" />
				</a>
			</td>
			<td>
				<a href="mailto:iilhomjonov777@gmail.com" target="_blank">
					<img src="https://github.com/gayanvoice/github-active-users-monitor/blob/master/public/images/icons/gmail.svg"
							 height="40" width="40" alt="Email" title="Email" />
				</a>
			</td>
			<td>
				<a href="https://github.com/iqbolshoh" target="_blank">
					<img src="https://raw.githubusercontent.com/rahuldkjain/github-profile-readme-generator/master/src/images/icons/Social/github.svg"
							 height="40" width="40" alt="GitHub" title="GitHub" />
				</a>
			</td>
			<td>
				<a href="https://www.linkedin.com/in/iqbolshoh/" target="_blank">
					<img src="https://github.com/gayanvoice/github-active-users-monitor/blob/master/public/images/icons/linkedin.svg"
							 height="40" width="40" alt="LinkedIn" title="LinkedIn" />
				</a>
			</td>
			<td>
				<a href="https://t.me/iqbolshoh_777" target="_blank">
					<img src="https://github.com/gayanvoice/github-active-users-monitor/blob/master/public/images/icons/telegram.svg"
							 height="40" width="40" alt="Telegram" title="Telegram" />
				</a>
			</td>
			<td>
				<a href="https://wa.me/998997799333" target="_blank">
					<img src="https://github.com/gayanvoice/github-active-users-monitor/blob/master/public/images/icons/whatsapp.svg"
							 height="40" width="40" alt="WhatsApp" title="WhatsApp" />
				</a>
			</td>
			<td>
				<a href="https://instagram.com/iqbolshoh_777" target="_blank">
					<img src="https://raw.githubusercontent.com/rahuldkjain/github-profile-readme-generator/master/src/images/icons/Social/instagram.svg"
							 height="40" width="40" alt="Instagram" title="Instagram" />
				</a>
			</td>
			<td>
				<a href="https://x.com/iqbolshoh_777" target="_blank">
					<img src="https://img.shields.io/badge/X-000000?style=for-the-badge&logo=x&logoColor=white"
							 height="40" width="40" alt="X" title="X (Twitter)" />
				</a>
			</td>
			<td>
				<a href="https://www.youtube.com/@Iqbolshoh_777" target="_blank">
					<img src="https://raw.githubusercontent.com/rahuldkjain/github-profile-readme-generator/master/src/images/icons/Social/youtube.svg"
							 height="40" width="40" alt="YouTube" title="YouTube" />
				</a>
			</td>
		</tr>
	</table>
</div>

## Notes

- The application uses PHP sessions for authentication and redirects users by role.
- Seeded accounts are defined in `database.sql`.
- Seller product image uploads are stored in `src/images/products/`.
- This repository does not currently include a separate license file.
