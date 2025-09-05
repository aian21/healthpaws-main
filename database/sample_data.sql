-- Sample data for HealthPaws database
USE healthpaws;

-- Insert sample users
INSERT INTO User (username, email, password, email_verified) VALUES 
('admin', 'admin@healthpaws.co', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', TRUE),
('vet1', 'dr.smith@healthpaws.co', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', TRUE),
('owner1', 'maria.rodriguez@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', TRUE),
('owner2', 'jamal.khan@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', TRUE),
('owner3', 'sam.thompson@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', TRUE);

-- Insert sample owners
INSERT INTO Owner (user_id, owner_fname, owner_lname, owner_phone) VALUES 
(3, 'Maria', 'Rodriguez', '(555) 111-2222'),
(4, 'Jamal', 'Khan', '(555) 333-4444'),
(5, 'Sam', 'Thompson', '(555) 555-6666');

-- Insert sample veterinarians
INSERT INTO Veterinarian (user_id, vet_fname, vet_lname, specialization, license_number, clinic_id) VALUES 
(2, 'Dr. Sarah', 'Smith', 'General Practice', 'VET-001-2024', 1);

-- Insert sample pets
INSERT INTO Pet (owner_id, default_clinic_id, pet_name, species, breed, gender, birthday, weight) VALUES 
(1, 1, 'Bella', 'Dog', 'Golden Retriever', 'Female', '2021-03-15', 65.5),
(2, 1, 'Max', 'Dog', 'Labrador Retriever', 'Male', '2019-07-22', 75.0),
(3, 1, 'Luna', 'Cat', 'Domestic Shorthair', 'Female', '2022-01-10', 12.0);

-- Insert sample appointments
INSERT INTO Appointment (pet_id, vet_id, clinic_id, appointment_date, appointment_time, status, reason) VALUES 
(1, 1, 1, CURDATE(), '09:00:00', 'Confirmed', 'Wellness exam'),
(2, 1, 1, CURDATE(), '09:30:00', 'Confirmed', 'Vaccination'),
(3, 1, 1, CURDATE(), '10:15:00', 'Pending', 'Dental consult'),
(1, 1, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '14:00:00', 'Confirmed', 'Follow-up'),
(2, 1, 1, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '11:00:00', 'Pending', 'Annual checkup');

-- Insert sample medical records
INSERT INTO MedicalRecord (pet_id, vet_id, visit_date, diagnosis, treatment, notes) VALUES 
(1, 1, DATE_SUB(CURDATE(), INTERVAL 30 DAY), 'Healthy', 'Routine wellness exam', 'Pet is in excellent health'),
(2, 1, DATE_SUB(CURDATE(), INTERVAL 45 DAY), 'Minor ear infection', 'Ear cleaning and medication', 'Follow up in 2 weeks'),
(3, 1, DATE_SUB(CURDATE(), INTERVAL 60 DAY), 'Dental tartar', 'Dental cleaning recommended', 'Schedule dental procedure');

-- Insert sample vaccinations
INSERT INTO Vaccination (vaccine_id, pet_id, vet_id, date_given, next_due_date) VALUES 
(1, 1, 1, DATE_SUB(CURDATE(), INTERVAL 6 MONTH), DATE_ADD(CURDATE(), INTERVAL 6 MONTH),
(2, 1, 1, DATE_SUB(CURDATE(), INTERVAL 3 MONTH), DATE_ADD(CURDATE(), INTERVAL 9 MONTH),
(1, 2, 1, DATE_SUB(CURDATE(), INTERVAL 8 MONTH), DATE_ADD(CURDATE(), INTERVAL 4 MONTH),
(2, 2, 1, DATE_SUB(CURDATE(), INTERVAL 5 MONTH), DATE_ADD(CURDATE(), INTERVAL 7 MONTH);

-- Insert sample payments
INSERT INTO Payment (appointment_id, amount, method, payment_status, payment_date) VALUES 
(1, 125.00, 'Credit Card', 'Paid', CURDATE()),
(2, 85.00, 'Cash', 'Pending', NULL),
(3, 150.00, 'Insurance', 'Pending', NULL);

-- Insert sample subscriptions
INSERT INTO Subscription (clinic_id, start_date, end_date, status, plan) VALUES 
(1, NOW(), DATE_ADD(NOW(), INTERVAL 1 YEAR), 'Active', 'Professional'),
(2, NOW(), DATE_ADD(NOW(), INTERVAL 1 YEAR), 'Active', 'Professional'),
(3, NOW(), DATE_ADD(NOW(), INTERVAL 1 YEAR), 'Active', 'Professional');


