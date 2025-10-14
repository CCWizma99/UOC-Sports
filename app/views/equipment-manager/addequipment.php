

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Equipment</title>
    <style>
        @import url("/uoc-sports/public/css/equipment-manager/index.css");
        @import url("/uoc-sports/public/css/equipment-manager/footer.css");
        @import url("/uoc-sports/public/css/equipment-manager/header.css");
        @import url("/uoc-sports/public/css/equipment-manager/form.css");
    </style>
</head>
<body>
    <?php require "../app/views/templates/equipment-manager/header.php"; ?>

    

    <div class="container">
        <div class="form-container">
            <h2>Add New Equipment</h2>
            <form action="/uoc-sports/public/equipment-manager/add-equipment" method="POST">
                <div class="form-group">
                    <label for="category">Category:</label>
                    <input type="text" id="category" name="category" required>
                </div>

                <div class="form-group">
                    <label for="code">Equipment Code:</label>
                    <input type="text" id="code" name="code" required>
                </div>

                <button type="submit" class="submit-btn">Add Equipment</button>
            </form>
        </div>
    </div>

    <?php require "../app/views/templates/equipment-manager/footer.php"; ?>
</body>
</html>