<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

requireAdmin();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']);
    if (isset($_POST['change_role'])) {
        try {
            $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $current_role = $stmt->fetchColumn();
            $new_role = $current_role === 'customer' ? 'admin' : 'customer';
            $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
            $stmt->execute([$new_role, $user_id]);
            $message = 'User role updated successfully.';
        } catch (PDOException $e) {
            $message = 'Failed to update role: ' . $e->getMessage();
        }
    } elseif (isset($_POST['delete_user'])) {
        try {
            // Prevent deleting self
            if ($user_id === $_SESSION['user_id']) {
                $message = 'Cannot delete your own account.';
            } else {
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                $message = 'User deleted successfully.';
            }
        } catch (PDOException $e) {
            $message = 'Failed to delete user: ' . $e->getMessage();
        }
    }
}

// Get all users
try {
    $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    $users = [];
    $message = 'Failed to load users.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Maison de Pâte</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <?php include '../includes/navbar.php'; ?>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <h2 class="text-3xl font-bold mb-6 animate__animated animate__fadeInDown">Manage Users</h2>

        <?php if ($message): ?>
            <p class="text-<?php echo strpos($message, 'successfully') !== false ? 'green' : 'red'; ?>-500 mb-4 animate__animated animate__fadeIn"><?php echo $message; ?></p>
        <?php endif; ?>

        <div class="bg-white rounded-lg shadow-xl overflow-hidden animate__animated animate__fadeInUp">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($users as $user): ?>
                        <tr class="animate__animated animate__zoomIn">
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo sanitize($user['username']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo sanitize($user['email']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo sanitize($user['role']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <form method="post" class="inline">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <button type="submit" name="change_role" class="text-[#4c2b1b] hover:text-[#3a1f14] hover:underline transition duration-300 mr-4">
                                        <?php echo $user['role'] === 'customer' ? 'Make Admin' : 'Make Customer'; ?>
                                    </button>
                                </form>
                                <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                                    <form method="post" class="inline" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" name="delete_user" class="text-[#4c2b1b] hover:text-[#3a1f14] hover:underline transition duration-300">Delete</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if (empty($users)): ?>
            <p class="text-center text-gray-600 mt-4 animate__animated animate__fadeIn">No users found.</p>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <?php include '../includes/footer.php'; ?>
</body>
</html>
