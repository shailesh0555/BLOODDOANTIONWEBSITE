-- Create the database
CREATE DATABASE IF NOT EXISTS BloodDonationDB;
USE BloodDonationDB;

-- Create the Donors table
CREATE TABLE IF NOT EXISTS Donors (
    donor_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    blood_type VARCHAR(5) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create the BloodRequests table
CREATE TABLE IF NOT EXISTS BloodRequests (
    request_id INT AUTO_INCREMENT PRIMARY KEY,
    requester_name VARCHAR(255) NOT NULL,
    blood_type_needed VARCHAR(5) NOT NULL,
    request_date DATE NOT NULL,
    status ENUM('Pending', 'Fulfilled') DEFAULT 'Pending',
    donor_id INT,
    FOREIGN KEY (donor_id) REFERENCES Donors(donor_id)
);

-- Insert sample data into the Donors table
INSERT INTO Donors (name, blood_type, email) VALUES
    ('John Doe', 'A+', 'john@example.com'),
    ('Jane Smith', 'B-', 'jane@example.com'),
    ('Michael Johnson', 'O+', 'michael@example.com'),
    ('Emily Davis', 'AB-', 'emily@example.com'),
    ('David Wilson', 'A-', 'david@example.com');

-- Insert sample data into the BloodRequests table
INSERT INTO BloodRequests (requester_name, blood_type_needed, request_date, donor_id) VALUES
    ('Hospital ABC', 'B+', '2023-11-01', 1),
    ('Red Cross Society', 'O-', '2023-11-05', 2),
    ('Community Clinic', 'A+', '2023-11-10', 3),
    ('Emergency Services', 'AB-', '2023-11-15', 4),
    ('Medical Center XYZ', 'O+', '2023-11-20', 5);

