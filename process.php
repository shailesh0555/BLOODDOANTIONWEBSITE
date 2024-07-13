
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Donation Management System - Processing Page</title>
    <link rel="stylesheet" href="style_process.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-image: url('bg4.png');
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .container {
            width: 80%;
            max-width: 600px;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            text-align: center;
        }

        h1 {
            color: #3498db;
        }

        h2 {
            color: #333;
        }

        .error {
            color: #e74c3c;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .success {
            color: #2ecc71;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .blood-drop {
            font-size: 30px;
            color: #c0392b;
            font-weight: bold;
        }

        #previousDonors {
            margin-top: 30px;
        }

        #previousDonors h2 {
            margin-bottom: 10px;
            color: #333;
        }

        #donorsList {
            list-style-type: none;
            padding: 0;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
        }

        #donorsList li {
            margin: 5px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        #requestAvailability {
            margin: 5px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        #requestAvailability h2 {
            color: #333;
        }

        #requestAvailability p {
            margin: 10px 0;
            color: #555;
        }
        
    </style>
</head>
<body>
    <div class="container">
        <h1>Blood Donation Management System</h1>
        <h2>Processing Page</h2>
        
        <?php
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "BloodDonationDB";
        
        $conn = new mysqli($servername, $username, $password, $dbname);
        $donorName = isset($_POST['name']) ? $_POST['name'] : '';
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Process donor registration form submission
            if (isset($_POST["name"])) {
                $name = $_POST["name"];
                $bloodType = isset($_POST["bloodType"]) ? $_POST["bloodType"] : null;
                $email = isset($_POST["email"]) ? $_POST["email"] : null;

                if ($bloodType === null || empty($email)) {
                    echo '<p class="error">Error: Blood type or email not provided.</p>';
                } else {
                    $checkExistingEmail = "SELECT * FROM Donors WHERE email = '$email'";
                    $result = $conn->query($checkExistingEmail);

                    if ($result->num_rows > 0) {
                        echo '<p class="error">Error: Email already exists.</p>';
                    } else {
                        $sql = "INSERT INTO Donors (name, blood_type, email) VALUES ('$name', '$bloodType', '$email')";

                        if ($conn->query($sql) === TRUE) {
                            echo '<p class="success">Thank you, ' . $donorName . ', for registering as a donor! Your contribution is invaluable.</p>';
                            echo '<div class="blood-drop">&#x1F489;</div>';
                        } else {
                            echo '<p class="error">Error: ' . $sql . "<br>" . $conn->error . '</p>';
                        }
                    }
                }
            }

            // Process blood request form submission
            if (isset($_POST["requesterName"])) {
                $requesterName = $_POST["requesterName"];
                $bloodTypeNeeded = isset($_POST["bloodTypeNeeded"]) ? $_POST["bloodTypeNeeded"] : null;
                $requestDate = isset($_POST["requestDate"]) ? $_POST["requestDate"] : null;

                if ($bloodTypeNeeded === null || empty($requestDate)) {
                    echo '<p class="error">Error: Blood type or request date not provided.</p>';
                } else {
                    // JOIN query
                    $availabilityQuery = "SELECT Donors.name FROM Donors
                                          LEFT JOIN BloodRequests ON Donors.donor_id = BloodRequests.donor_id
                                          WHERE Donors.blood_type = '$bloodTypeNeeded'
                                          AND (BloodRequests.status IS NULL OR BloodRequests.status = 'Fulfilled')";
                    $availabilityResult = $conn->query($availabilityQuery);

                    if ($availabilityResult->num_rows > 0) {
                        $donorNames = [];
                        while ($row = $availabilityResult->fetch_assoc()) {
                            $donorNames[] = $row["name"];
                        }
                        $donorList = implode(", ", $donorNames);
                        echo '<div id="requestAvailability">';
                        echo '<h2>Donors available for the requested blood type:</h2>';
                        echo '<p>' . $donorList . '</p>';
                        echo '</div>';
                    } else {
                        echo '<div id="requestAvailability">';
                        echo '<h2>No donors available for the requested blood type.</h2>';
                        echo '</div>';
                    }
                }
            }
            // Process email change form submission
            if (isset($_POST["newEmail"]) && isset($_POST["donorId"])) {
                $newEmail = $_POST["newEmail"];
                $donorId = $_POST["donorId"];

                $updateEmailQuery = "UPDATE Donors SET email = '$newEmail' WHERE donor_id = $donorId";

                if ($conn->query($updateEmailQuery) === TRUE) {
                    echo '<p class="success">Email successfully changed!</p>';
                } else {
                    echo '<p class="error">Error updating email: ' . $conn->error . '</p>';
                }
            }

            // Process donor deletion
            if (isset($_POST["donorId"])) {
                $donorId = $_POST["donorId"];

                
                $updateBloodRequestsQuery = "UPDATE BloodRequests SET donor_id = NULL WHERE donor_id = $donorId";

                if ($conn->query($updateBloodRequestsQuery) === TRUE) {
                    
                    $deleteDonorQuery = "DELETE FROM Donors WHERE donor_id = $donorId";

                    if ($conn->query($deleteDonorQuery) === TRUE) {
                        echo '<p class="success">Donor deleted successfully!</p>';
                    } else {
                        echo '<p class="error">Error deleting donor: ' . $conn->error . '</p>';
                    }
                } else {
                    echo '<p class="error">Error updating BloodRequests: ' . $conn->error . '</p>';
                }
            }
        }

        // Display previous donors
        $previousDonorsQuery = "SELECT name, blood_type FROM Donors";
        $previousDonorsResult = $conn->query($previousDonorsQuery);

        if ($previousDonorsResult->num_rows > 0) {
            echo '<div id="previousDonors">';
            echo '<h2>Previous Donors</h2>';
            echo '<ul id="donorsList">';
            while ($row = $previousDonorsResult->fetch_assoc()) {
                echo '<li><strong>' . $row["name"] . '</strong> - Blood Type: ' . $row["blood_type"] . '</li>';
            }
            echo '</ul>';
            echo '</div>';
        }


        $conn->close();
        ?>
    </div>
</body>
</html>
