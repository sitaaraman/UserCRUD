# PHP User Management System (Vanilla PHP + MySQLi)

A simple **User Management System** built using **pure PHP (procedural)**, **MySQL (mysqli)**, and **Bootstrap 5**.  
This project does **not use JavaScript**, **PDO**, or any framework.

It demonstrates **CRUD operations**, **file uploads**, **checkbox handling**, **dropdown relations**, and **bulk delete functionality**.

---

## ✨ Features

### Users
- Add / Edit / Delete Users
- Bulk delete users
- Upload profile picture
- Auto-delete old images on update/delete
- Select multiple hobbies (checkbox)
- Select category (dropdown)
- PHP-based delete confirmation (NO JavaScript)

### Hobbies
- Add hobbies
- Edit hobbies
- Delete hobbies

### Categories
- Add categories
- Edit categories
- Delete categories

---

## 🛠️ Technologies Used

- PHP (Procedural)
- MySQL (mysqli)
- HTML5
- Bootstrap 5
- No JavaScript
- No PDO
- No Frameworks

---

## 📂 Project Structure

```
/project-root
│
├── uploads/ # Profile images
│
├── categories.php # Category CRUD
├── hobbies.php # Hobby CRUD
├── users.php # User CRUD + upload + bulk delete
│
├── config.php # Database connection
├── db.sql # Database schema
├── README.md # Documentation

``` 