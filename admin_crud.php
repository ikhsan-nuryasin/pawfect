<?php
session_start();
require 'db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

$message = '';
$messageType = '';
$section = $_GET['section'] ?? 'products';
$action = $_GET['action'] ?? 'list';
$editId = isset($_GET['id']) ? intval($_GET['id']) : null;

// ============== PRODUCTS CRUD ==============

// Create Product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_type'])) {
    
    if ($_POST['action_type'] === 'add_product') {
        $nama = trim($_POST['nama_produk'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $harga = floatval($_POST['harga'] ?? 0);
        $kategori = trim($_POST['kategori'] ?? '');
        $gambar = trim($_POST['gambar'] ?? '');

        if ($nama && $deskripsi && $harga > 0 && $kategori) {
            $stmt = $conn->prepare("INSERT INTO produk (nama_produk, deskripsi, harga, kategori, gambar) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssdss", $nama, $deskripsi, $harga, $kategori, $gambar);
            
            if ($stmt->execute()) {
                $message = "Produk berhasil ditambahkan!";
                $messageType = "success";
                $action = 'list';
            } else {
                $message = "Error: " . $conn->error;
                $messageType = "error";
            }
            $stmt->close();
        } else {
            $message = "Semua field harus diisi dan harga harus > 0!";
            $messageType = "error";
        }
    }
    
    // Update Product
    elseif ($_POST['action_type'] === 'update_product') {
        $id = intval($_POST['product_id']);
        $nama = trim($_POST['nama_produk'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $harga = floatval($_POST['harga'] ?? 0);
        $kategori = trim($_POST['kategori'] ?? '');
        $gambar = trim($_POST['gambar'] ?? '');

        if ($nama && $deskripsi && $harga > 0 && $kategori) {
            $stmt = $conn->prepare("UPDATE produk SET nama_produk=?, deskripsi=?, harga=?, kategori=?, gambar=? WHERE id=?");
            $stmt->bind_param("ssdssi", $nama, $deskripsi, $harga, $kategori, $gambar, $id);
            
            if ($stmt->execute()) {
                $message = "Produk berhasil diperbarui!";
                $messageType = "success";
                $action = 'list';
            } else {
                $message = "Error: " . $conn->error;
                $messageType = "error";
            }
            $stmt->close();
        } else {
            $message = "Semua field harus diisi!";
            $messageType = "error";
        }
    }
    
    // Delete Product
    elseif ($_POST['action_type'] === 'delete_product') {
        $id = intval($_POST['product_id']);
        $stmt = $conn->prepare("DELETE FROM produk WHERE id=?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $message = "Produk berhasil dihapus!";
            $messageType = "success";
        } else {
            $message = "Error: " . $conn->error;
            $messageType = "error";
        }
        $stmt->close();
    }
    
    // Add User
    elseif ($_POST['action_type'] === 'add_user') {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $nama = trim($_POST['nama'] ?? '');

        if ($username && $email && $password && $nama) {
            // Check if username exists
            $checkStmt = $conn->prepare("SELECT id FROM user WHERE username=?");
            $checkStmt->bind_param("s", $username);
            $checkStmt->execute();
            
            if ($checkStmt->get_result()->num_rows > 0) {
                $message = "Username sudah terdaftar!";
                $messageType = "error";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO user (username, email, password, nama) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $username, $email, $hashedPassword, $nama);
                
                if ($stmt->execute()) {
                    $message = "User berhasil ditambahkan!";
                    $messageType = "success";
                    $action = 'list';
                } else {
                    $message = "Error: " . $conn->error;
                    $messageType = "error";
                }
                $stmt->close();
            }
            $checkStmt->close();
        } else {
            $message = "Semua field harus diisi!";
            $messageType = "error";
        }
    }
    
    // Update User
    elseif ($_POST['action_type'] === 'update_user') {
        $id = intval($_POST['user_id']);
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $nama = trim($_POST['nama'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if ($username && $email && $nama) {
            if ($password) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE user SET username=?, email=?, password=?, nama=? WHERE id=?");
                $stmt->bind_param("ssssi", $username, $email, $hashedPassword, $nama, $id);
            } else {
                $stmt = $conn->prepare("UPDATE user SET username=?, email=?, nama=? WHERE id=?");
                $stmt->bind_param("sssi", $username, $email, $nama, $id);
            }
            
            if ($stmt->execute()) {
                $message = "User berhasil diperbarui!";
                $messageType = "success";
                $action = 'list';
            } else {
                $message = "Error: " . $conn->error;
                $messageType = "error";
            }
            $stmt->close();
        } else {
            $message = "Semua field harus diisi!";
            $messageType = "error";
        }
    }
    
    // Delete User
    elseif ($_POST['action_type'] === 'delete_user') {
        $id = intval($_POST['user_id']);
        $stmt = $conn->prepare("DELETE FROM user WHERE id=?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $message = "User berhasil dihapus!";
            $messageType = "success";
        } else {
            $message = "Error: " . $conn->error;
            $messageType = "error";
        }
        $stmt->close();
    }
    
    // ============== ARTICLES CRUD ==============
    
    // Create Article
    elseif ($_POST['action_type'] === 'add_article') {
        $judul = trim($_POST['judul'] ?? '');
        $konten = trim($_POST['konten'] ?? '');
        $gambar = trim($_POST['gambar'] ?? '');

        if ($judul && $konten) {
            $stmt = $conn->prepare("INSERT INTO artikel (judul, konten, gambar) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $judul, $konten, $gambar);
            
            if ($stmt->execute()) {
                $message = "Artikel berhasil ditambahkan!";
                $messageType = "success";
                $action = 'list';
            } else {
                $message = "Error: " . $conn->error;
                $messageType = "error";
            }
            $stmt->close();
        } else {
            $message = "Judul dan konten harus diisi!";
            $messageType = "error";
        }
    }
    
    // Update Article
    elseif ($_POST['action_type'] === 'update_article') {
        $id = intval($_POST['article_id']);
        $judul = trim($_POST['judul'] ?? '');
        $konten = trim($_POST['konten'] ?? '');
        $gambar = trim($_POST['gambar'] ?? '');

        if ($judul && $konten) {
            $stmt = $conn->prepare("UPDATE artikel SET judul=?, konten=?, gambar=? WHERE id=?");
            $stmt->bind_param("sssi", $judul, $konten, $gambar, $id);
            
            if ($stmt->execute()) {
                $message = "Artikel berhasil diperbarui!";
                $messageType = "success";
                $action = 'list';
            } else {
                $message = "Error: " . $conn->error;
                $messageType = "error";
            }
            $stmt->close();
        } else {
            $message = "Semua field harus diisi!";
            $messageType = "error";
        }
    }
    
    // Delete Article
    elseif ($_POST['action_type'] === 'delete_article') {
        $id = intval($_POST['article_id']);
        $stmt = $conn->prepare("DELETE FROM artikel WHERE id=?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $message = "Artikel berhasil dihapus!";
            $messageType = "success";
        } else {
            $message = "Error: " . $conn->error;
            $messageType = "error";
        }
        $stmt->close();
    }
}

// Get product for edit
$productData = null;
if ($section === 'products' && $action === 'edit' && $editId) {
    $stmt = $conn->prepare("SELECT * FROM produk WHERE id=?");
    $stmt->bind_param("i", $editId);
    $stmt->execute();
    $productData = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Get user for edit
$userData = null;
if ($section === 'users' && $action === 'edit' && $editId) {
    $stmt = $conn->prepare("SELECT * FROM user WHERE id=?");
    $stmt->bind_param("i", $editId);
    $stmt->execute();
    $userData = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Get all products
$products = [];
if ($section === 'products') {
    $result = $conn->query("SELECT * FROM produk ORDER BY id DESC");
    $products = $result->fetch_all(MYSQLI_ASSOC);
}

// Get all users
$users = [];
if ($section === 'users') {
    $result = $conn->query("SELECT id, username, email, nama FROM user ORDER BY id DESC");
    $users = $result->fetch_all(MYSQLI_ASSOC);
}

// Get article for edit
$articleData = null;
if ($section === 'articles' && $action === 'edit' && $editId) {
    $stmt = $conn->prepare("SELECT * FROM artikel WHERE id=?");
    $stmt->bind_param("i", $editId);
    $stmt->execute();
    $articleData = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Get all articles
$articles = [];
if ($section === 'articles') {
    $result = $conn->query("SELECT * FROM artikel ORDER BY id DESC");
    if ($result) {
        $articles = $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin CRUD - Pawfect</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f0f0f0;
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .header h1 {
            color: #333;
        }

        .header a {
            display: inline-block;
            padding: 10px 20px;
            background: #9a6f60;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .nav-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .nav-tabs a {
            display: inline-block;
            padding: 10px 20px;
            background: white;
            color: #333;
            text-decoration: none;
            border-radius: 5px;
            border: 2px solid #ddd;
            cursor: pointer;
        }

        .nav-tabs a.active {
            background: #9a6f60;
            color: white;
            border-color: #9a6f60;
        }

        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-weight: 600;
        }

        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .btn-group {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #9a6f60;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .btn:hover {
            background: #8b5f50;
        }

        .btn-secondary {
            background: #6c757d;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .btn-danger {
            background: #dc3545;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        .btn-success {
            background: #28a745;
        }

        .btn-success:hover {
            background: #218838;
        }

        .form-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: inherit;
            font-size: 14px;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .table-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f8f9fa;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #333;
            border-bottom: 2px solid #ddd;
        }

        td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
        }

        tr:hover {
            background: #f9f9f9;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .action-buttons a,
        .action-buttons button {
            padding: 6px 12px;
            font-size: 12px;
            text-decoration: none;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        .btn-edit {
            background: #007bff;
            color: white;
        }

        .btn-edit:hover {
            background: #0056b3;
        }

        .btn-delete {
            background: #dc3545;
            color: white;
        }

        .btn-delete:hover {
            background: #c82333;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #999;
        }

        .form-back {
            text-align: center;
            margin-top: 20px;
        }

        .form-back a {
            color: #9a6f60;
            text-decoration: none;
        }

        .form-back a:hover {
            text-decoration: underline;
        }

        .image-preview {
            margin-top: 10px;
            max-width: 150px;
        }

        .image-preview img {
            max-width: 100%;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>Admin CRUD Panel</h1>
            <a href="admin_logout.php">Logout</a>
        </div>

        <!-- Message -->
        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Navigation Tabs -->
        <div class="nav-tabs">
            <a href="admin_crud.php?section=products" class="<?php echo $section === 'products' ? 'active' : ''; ?>">
                📦 Kelola Produk
            </a>
            <a href="admin_crud.php?section=users" class="<?php echo $section === 'users' ? 'active' : ''; ?>">
                👥 Kelola User
            </a>
            <a href="admin_crud.php?section=articles" class="<?php echo $section === 'articles' ? 'active' : ''; ?>">
                📰 Kelola Artikel
            </a>
        </div>

        <!-- PRODUCTS SECTION -->
        <?php if ($section === 'products'): ?>

            <?php if ($action === 'list'): ?>
                <!-- Products List -->
                <div class="btn-group">
                    <a href="admin_crud.php?section=products&action=add" class="btn btn-success">+ Tambah Produk</a>
                </div>

                <?php if (!empty($products)): ?>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama Produk</th>
                                    <th>Kategori</th>
                                    <th>Harga</th>
                                    <th>Deskripsi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td><?php echo $product['id']; ?></td>
                                        <td><?php echo htmlspecialchars($product['nama_produk']); ?></td>
                                        <td><?php echo htmlspecialchars($product['kategori']); ?></td>
                                        <td>Rp <?php echo number_format($product['harga'], 0, ',', '.'); ?></td>
                                        <td><?php echo htmlspecialchars(substr($product['deskripsi'], 0, 50)) . '...'; ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="admin_crud.php?section=products&action=edit&id=<?php echo $product['id']; ?>" class="btn btn-edit">Edit</a>
                                                <form method="POST" style="display:inline;" onsubmit="return confirm('Yakin hapus produk ini?');">
                                                    <input type="hidden" name="action_type" value="delete_product">
                                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                                    <button type="submit" class="btn btn-delete">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="no-data">Belum ada produk</div>
                <?php endif; ?>

            <?php elseif ($action === 'add' || $action === 'edit'): ?>
                <!-- Add/Edit Product Form -->
                <div class="form-container">
                    <h2><?php echo $action === 'add' ? 'Tambah Produk' : 'Edit Produk'; ?></h2>
                    <form method="POST">
                        <input type="hidden" name="action_type" value="<?php echo $action === 'add' ? 'add_product' : 'update_product'; ?>">
                        <?php if ($action === 'edit'): ?>
                            <input type="hidden" name="product_id" value="<?php echo $editId; ?>">
                        <?php endif; ?>

                        <div class="form-group">
                            <label>Nama Produk</label>
                            <input type="text" name="nama_produk" required value="<?php echo htmlspecialchars($productData['nama_produk'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label>Deskripsi</label>
                            <textarea name="deskripsi" required><?php echo htmlspecialchars($productData['deskripsi'] ?? ''); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label>Harga</label>
                            <input type="number" name="harga" step="0.01" required value="<?php echo $productData['harga'] ?? ''; ?>">
                        </div>

                        <div class="form-group">
                            <label>Kategori</label>
                            <select name="kategori" required>
                                <option value="">-- Pilih Kategori --</option>
                                <option value="food" <?php echo (($productData['kategori'] ?? '') === 'food') ? 'selected' : ''; ?>>Food</option>
                                <option value="accessories" <?php echo (($productData['kategori'] ?? '') === 'accessories') ? 'selected' : ''; ?>>Accessories</option>
                                <option value="petcare" <?php echo (($productData['kategori'] ?? '') === 'petcare') ? 'selected' : ''; ?>>Pet Care</option>
                                <option value="toys" <?php echo (($productData['kategori'] ?? '') === 'toys') ? 'selected' : ''; ?>>Toys</option>
                                <option value="grooming" <?php echo (($productData['kategori'] ?? '') === 'grooming') ? 'selected' : ''; ?>>Grooming</option>
                                <option value="health" <?php echo (($productData['kategori'] ?? '') === 'health') ? 'selected' : ''; ?>>Health</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>URL Gambar</label>
                            <input type="url" name="gambar" value="<?php echo htmlspecialchars($productData['gambar'] ?? ''); ?>">
                            <?php if ($productData && !empty($productData['gambar'])): ?>
                                <div class="image-preview">
                                    <img src="<?php echo htmlspecialchars($productData['gambar']); ?>" alt="Preview">
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="form-buttons">
                            <button type="submit" class="btn btn-success"><?php echo $action === 'add' ? 'Tambah' : 'Perbarui'; ?></button>
                            <a href="admin_crud.php?section=products" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                    <div class="form-back">
                        <a href="admin_crud.php?section=products">← Kembali ke Daftar Produk</a>
                    </div>
                </div>
            <?php endif; ?>

        <!-- USERS SECTION -->
        <?php elseif ($section === 'users'): ?>

            <?php if ($action === 'list'): ?>
                <!-- Users List -->
                <div class="btn-group">
                    <a href="admin_crud.php?section=users&action=add" class="btn btn-success">+ Tambah User</a>
                </div>

                <?php if (!empty($users)): ?>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Nama</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?php echo $user['id']; ?></td>
                                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td><?php echo htmlspecialchars($user['nama']); ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="admin_crud.php?section=users&action=edit&id=<?php echo $user['id']; ?>" class="btn btn-edit">Edit</a>
                                                <form method="POST" style="display:inline;" onsubmit="return confirm('Yakin hapus user ini?');">
                                                    <input type="hidden" name="action_type" value="delete_user">
                                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                    <button type="submit" class="btn btn-delete">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="no-data">Belum ada user</div>
                <?php endif; ?>

            <?php elseif ($action === 'add' || $action === 'edit'): ?>
                <!-- Add/Edit User Form -->
                <div class="form-container">
                    <h2><?php echo $action === 'add' ? 'Tambah User' : 'Edit User'; ?></h2>
                    <form method="POST">
                        <input type="hidden" name="action_type" value="<?php echo $action === 'add' ? 'add_user' : 'update_user'; ?>">
                        <?php if ($action === 'edit'): ?>
                            <input type="hidden" name="user_id" value="<?php echo $editId; ?>">
                        <?php endif; ?>

                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="username" required value="<?php echo htmlspecialchars($userData['username'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" required value="<?php echo htmlspecialchars($userData['email'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama" required value="<?php echo htmlspecialchars($userData['nama'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label>Password <?php echo $action === 'edit' ? '(Kosongkan jika tidak ingin mengubah)' : ''; ?></label>
                            <input type="password" name="password" <?php echo $action === 'add' ? 'required' : ''; ?>>
                        </div>

                        <div class="form-buttons">
                            <button type="submit" class="btn btn-success"><?php echo $action === 'add' ? 'Tambah' : 'Perbarui'; ?></button>
                            <a href="admin_crud.php?section=users" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                    <div class="form-back">
                        <a href="admin_crud.php?section=users">← Kembali ke Daftar User</a>
                    </div>
                </div>
            <?php endif; ?>

        <!-- ARTICLES SECTION -->
        <?php elseif ($section === 'articles'): ?>

            <?php if ($action === 'list'): ?>
                <!-- Articles List -->
                <div class="btn-group">
                    <a href="admin_crud.php?section=articles&action=add" class="btn btn-success">+ Tambah Artikel</a>
                </div>

                <?php if (!empty($articles)): ?>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Judul</th>
                                    <th>Konten</th>
                                    <th>Gambar</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($articles as $article): ?>
                                    <tr>
                                        <td><?php echo $article['id']; ?></td>
                                        <td><?php echo htmlspecialchars($article['judul']); ?></td>
                                        <td><?php echo htmlspecialchars(substr($article['konten'], 0, 50)) . '...'; ?></td>
                                        <td><?php echo !empty($article['gambar']) ? '✓' : '✗'; ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="admin_crud.php?section=articles&action=edit&id=<?php echo $article['id']; ?>" class="btn btn-edit">Edit</a>
                                                <form method="POST" style="display:inline;" onsubmit="return confirm('Yakin hapus artikel ini?');">
                                                    <input type="hidden" name="action_type" value="delete_article">
                                                    <input type="hidden" name="article_id" value="<?php echo $article['id']; ?>">
                                                    <button type="submit" class="btn btn-delete">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="no-data">Belum ada artikel</div>
                <?php endif; ?>

            <?php elseif ($action === 'add' || $action === 'edit'): ?>
                <!-- Add/Edit Article Form -->
                <div class="form-container">
                    <h2><?php echo $action === 'add' ? 'Tambah Artikel' : 'Edit Artikel'; ?></h2>
                    <form method="POST">
                        <input type="hidden" name="action_type" value="<?php echo $action === 'add' ? 'add_article' : 'update_article'; ?>">
                        <?php if ($action === 'edit'): ?>
                            <input type="hidden" name="article_id" value="<?php echo $editId; ?>">
                        <?php endif; ?>

                        <div class="form-group">
                            <label>Judul Artikel</label>
                            <input type="text" name="judul" required value="<?php echo htmlspecialchars($articleData['judul'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label>Konten</label>
                            <textarea name="konten" required><?php echo htmlspecialchars($articleData['konten'] ?? ''); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label>URL Gambar</label>
                            <input type="url" name="gambar" value="<?php echo htmlspecialchars($articleData['gambar'] ?? ''); ?>">
                            <?php if ($articleData && !empty($articleData['gambar'])): ?>
                                <div class="image-preview">
                                    <img src="<?php echo htmlspecialchars($articleData['gambar']); ?>" alt="Preview">
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="form-buttons">
                            <button type="submit" class="btn btn-success"><?php echo $action === 'add' ? 'Tambah' : 'Perbarui'; ?></button>
                            <a href="admin_crud.php?section=articles" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                    <div class="form-back">
                        <a href="admin_crud.php?section=articles">← Kembali ke Daftar Artikel</a>
                    </div>
                </div>
            <?php endif; ?>

        <?php endif; ?>
    </div>
</body>
</html>
