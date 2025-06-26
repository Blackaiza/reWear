<?php
session_start();
include 'database.php';

// Ensure user is logged in
if (!isset($_SESSION['no_matrik'])) {
    header("Location: login.php");
    exit();
}

// Check if the 'id' parameter is passed via GET
if (isset($_GET['id'])) {
    $id = $_GET['id']; // Get the clothing item's ID from the query string

    // Database connection
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    try {
        // Prepare the DELETE query
        $stmt = $conn->prepare("DELETE FROM tbl_pakaian WHERE idPakaian = ?");
        $stmt->execute([$id]); // Execute the query with the item ID
        
        // Optionally, you can delete the image from the server if you want to fully remove the item
        // Retrieve the image path before deleting
        $stmt = $conn->prepare("SELECT gambar FROM tbl_pakaian WHERE idPakaian = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch();

        // Check if the image exists and delete it
        if ($item && !empty($item['gambar'])) {
            $imagePath = $item['gambar'];
            if (file_exists($imagePath)) {
                unlink($imagePath); // Delete the image from the server
            }
        }

        // Redirect to the list page after deletion
        header("Location: senaraipakaian.php");
        exit();
    } catch (PDOException $e) {
        // Handle any errors
        die("<script>alert('Error: " . $e->getMessage() . "'); window.location.href='senaraipakaian.php';</script>");
    }
} else {
    // If the 'id' parameter is not passed, redirect back to the list
    header("Location: senaraipakaian.php");
    exit();
}
?>
