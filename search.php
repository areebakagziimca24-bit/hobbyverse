
<?php
include 'db_connect.php';
$q = mysqli_real_escape_string($conn, $_GET['q']);


$result = mysqli_query($conn, "SELECT * FROM hobbies WHERE hobby_name LIKE '%$q%'");
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Search Results - Hobbyverse</title>
  <style>
   
    body { font-family:'Poppins', sans-serif; padding:20px; }
    .card { border:1px solid #ccc; padding:20px; margin:10px; border-radius:10px; }
    a { text-decoration:none; color:#ff4c8b; }
  </style>
</head>
<body>
  <h1>Search Results for "<?php echo htmlspecialchars($q); ?>"</h1>
  <?php if (mysqli_num_rows($result) > 0): ?>
    <?php while($row = mysqli_fetch_assoc($result)): ?>
      <div class="card">
        <h2><?php echo $row['hobby_name']; ?></h2>
        <p><?php echo $row['description']; ?></p>
        <a href="hobby.php?hobby_id=<?php echo $row['hobby_id']; ?>">Explore</a>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <p>No hobbies found. Try another search.</p>
  <?php endif; ?>
</body>
</html>
