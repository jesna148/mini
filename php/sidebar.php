<?php
// Ensure session and DB connection are available
if (!isset($profileLink)) $profileLink = "profile_view.php";

// Default notification counts if not set
$eventNotifCount = isset($eventNotifCount) ? $eventNotifCount : 0;
$clubNotifCount = isset($clubNotifCount) ? $clubNotifCount : 0;
$generalNotifCount = isset($generalNotifCount) ? $generalNotifCount : 0;
$totalNotifCount = $eventNotifCount + $clubNotifCount + $generalNotifCount;
?>

<!-- ✅ Sidebar Styles + FontAwesome link -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<div class="sidebar">
    <h2>📚 Student Panel</h2>

    <a href="student_home.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'student_home.php' ? 'active' : ''; ?>">
        <i class="fas fa-home"></i><span>Dashboard</span>
        <?php if ($generalNotifCount > 0): ?>
            <span class="badge"><?php echo $generalNotifCount; ?></span>
        <?php endif; ?>
    </a>

    <a href="events.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'events.php' ? 'active' : ''; ?>">
        <i class="fas fa-calendar-alt"></i><span>Events</span>
        <?php if ($eventNotifCount > 0): ?>
            <span class="badge"><?php echo $eventNotifCount; ?></span>
        <?php endif; ?>
    </a>

    <a href="clubs.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'clubs.php' ? 'active' : ''; ?>">
        <i class="fas fa-users"></i><span>Clubs</span>
        <?php if ($clubNotifCount > 0): ?>
            <span class="badge"><?php echo $clubNotifCount; ?></span>
        <?php endif; ?>
    </a>

    <a href="notification_std.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'notification_std.php' ? 'active' : ''; ?>">
        <i class="fas fa-bell"></i><span>Notifications</span>
        <?php if ($totalNotifCount > 0): ?>
            <span class="badge"><?php echo $totalNotifCount; ?></span>
        <?php endif; ?>
    </a>

    <a href="feedback.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'feedback.php' ? 'active' : ''; ?>">
        <i class="fas fa-cog"></i><span>Feedback</span>
    </a>

    <a href="<?php echo $profileLink; ?>" class="<?php echo basename($_SERVER['PHP_SELF']) == 'profile_view.php' || basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>">
        <i class="fas fa-user"></i><span>Profile</span>
    </a>

    <a href="page1.html"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
</div>

<style>
/* Sidebar Styles */
.sidebar {
    width: 250px;
    background: #1e293b;
    color: #fff;
    height: 100vh;
    padding: 24px 0 0 0;
    position: fixed;
    top: 0;
    left: 0;
    box-shadow: 2px 0 12px rgba(0,0,0,0.08);
    display: flex;
    flex-direction: column;
    z-index: 100;
}

.sidebar h2 {
    text-align: center;
    font-size: 24px;
    margin-bottom: 32px;
    font-weight: 700;
    letter-spacing: 1px;
    color: #fff;
}

.sidebar a {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    padding: 14px 28px;
    color: #cbd5e1;
    text-decoration: none;
    font-size: 17px;
    transition: background 0.2s, color 0.2s;
    border-radius: 0 24px 24px 0;
    margin-bottom: 2px;
}

.sidebar a i {
    margin-right: 14px;
    font-size: 20px;
    min-width: 24px;
    text-align: center;
}

.sidebar a span {
    flex-grow: 1;
}

.sidebar a.active,
.sidebar a:hover {
    background: #2563eb;
    color: #fff;
}

.sidebar a .badge {
    margin-left: auto;
    background: #e63946;
    color: #fff;
    font-size: 13px;
    padding: 2px 10px;
    border-radius: 12px;
    font-weight: 600;
    min-width: 24px;
    text-align: center;
    display: inline-block;
}

@media (max-width: 600px) {
    .sidebar {
        width: 100vw;
        height: auto;
        position: static;
        flex-direction: row;
        padding: 0;
        box-shadow: none;
    }
    .sidebar h2 {
        display: none;
    }
    .sidebar a {
        padding: 10px 8px;
        font-size: 15px;
        border-radius: 0;
        margin-bottom: 0;
        justify-content: center;
    }
}
</style>
