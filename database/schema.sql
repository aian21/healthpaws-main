-- HealthPaws Database Schema
-- Create database
CREATE DATABASE IF NOT EXISTS healthpaws;
USE healthpaws;

-- Role Table
CREATE TABLE Role (
    role_id INT PRIMARY KEY AUTO_INCREMENT,
    role_name VARCHAR(255) NOT NULL
);

-- User Table
CREATE TABLE User (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(255) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    user_photo VARCHAR(255),
    email_verified BOOLEAN DEFAULT FALSE,
    verification_code VARCHAR(6),
    verification_expires TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- UserRole Table
CREATE TABLE UserRole (
    userrole_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    role_id INT,
    FOREIGN KEY (user_id) REFERENCES User(user_id),
    FOREIGN KEY (role_id) REFERENCES Role(role_id)
);

-- Clinic Table
CREATE TABLE Clinic (
    clinic_id INT PRIMARY KEY AUTO_INCREMENT,
    clinic_name VARCHAR(255) NOT NULL,
    address VARCHAR(255),
    clinic_phone VARCHAR(255),
    clinic_email VARCHAR(255),
    clinic_subdomain VARCHAR(255) UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- UserClinic Table
CREATE TABLE UserClinic (
    userclinic_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    clinic_id INT,
    role_id INT,
    FOREIGN KEY (user_id) REFERENCES User(user_id),
    FOREIGN KEY (clinic_id) REFERENCES Clinic(clinic_id),
    FOREIGN KEY (role_id) REFERENCES Role(role_id)
);

-- Owner Table
CREATE TABLE Owner (
    owner_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    owner_fname VARCHAR(255) NOT NULL,
    owner_lname VARCHAR(255) NOT NULL,
    owner_phone VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES User(user_id)
);

-- Pet Table
CREATE TABLE Pet (
    pet_id INT PRIMARY KEY AUTO_INCREMENT,
    owner_id INT,
    default_clinic_id INT,
    pet_name VARCHAR(255) NOT NULL,
    species VARCHAR(255),
    breed VARCHAR(255),
    gender VARCHAR(255),
    birthday DATE,
    weight DECIMAL(6,2),
    pet_photo VARCHAR(255),
    FOREIGN KEY (owner_id) REFERENCES Owner(owner_id),
    FOREIGN KEY (default_clinic_id) REFERENCES Clinic(clinic_id)
);

-- Veterinarian Table
CREATE TABLE Veterinarian (
    vet_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    vet_fname VARCHAR(255) NOT NULL,
    vet_lname VARCHAR(255) NOT NULL,
    specialization VARCHAR(255),
    license_number VARCHAR(255),
    clinic_id INT,
    FOREIGN KEY (user_id) REFERENCES User(user_id),
    FOREIGN KEY (clinic_id) REFERENCES Clinic(clinic_id)
);

-- Vaccine Table
CREATE TABLE Vaccine (
    vaccine_id INT PRIMARY KEY AUTO_INCREMENT,
    vaccine_name VARCHAR(255) NOT NULL,
    description TEXT
);

-- Vaccination Table
CREATE TABLE Vaccination (
    vaccination_id INT PRIMARY KEY AUTO_INCREMENT,
    vaccine_id INT,
    pet_id INT,
    vet_id INT,
    date_given DATE,
    next_due_date DATE,
    FOREIGN KEY (vaccine_id) REFERENCES Vaccine(vaccine_id),
    FOREIGN KEY (pet_id) REFERENCES Pet(pet_id),
    FOREIGN KEY (vet_id) REFERENCES Veterinarian(vet_id)
);

-- MedicalRecord Table
CREATE TABLE MedicalRecord (
    record_id INT PRIMARY KEY AUTO_INCREMENT,
    pet_id INT,
    vet_id INT,
    visit_date DATE,
    diagnosis TEXT,
    treatment TEXT,
    prescription TEXT,
    notes TEXT,
    FOREIGN KEY (pet_id) REFERENCES Pet(pet_id),
    FOREIGN KEY (vet_id) REFERENCES Veterinarian(vet_id)
);

-- Appointment Table
CREATE TABLE Appointment (
    appointment_id INT PRIMARY KEY AUTO_INCREMENT,
    pet_id INT,
    vet_id INT,
    clinic_id INT,
    appointment_date DATE,
    appointment_time TIME,
    status VARCHAR(255) DEFAULT 'Pending',
    reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pet_id) REFERENCES Pet(pet_id),
    FOREIGN KEY (vet_id) REFERENCES Veterinarian(vet_id),
    FOREIGN KEY (clinic_id) REFERENCES Clinic(clinic_id)
);

-- Payment Table
CREATE TABLE Payment (
    payment_id INT PRIMARY KEY AUTO_INCREMENT,
    appointment_id INT,
    amount DECIMAL(10,2),
    method VARCHAR(255),
    payment_status VARCHAR(255) DEFAULT 'Pending',
    payment_date DATE,
    FOREIGN KEY (appointment_id) REFERENCES Appointment(appointment_id)
);

-- Subscription Table
CREATE TABLE Subscription (
    subscription_id INT PRIMARY KEY AUTO_INCREMENT,
    clinic_id INT,
    start_date TIMESTAMP,
    end_date TIMESTAMP,
    status VARCHAR(255) DEFAULT 'Active',
    plan VARCHAR(255),
    FOREIGN KEY (clinic_id) REFERENCES Clinic(clinic_id)
);

-- Insert default roles
INSERT INTO Role (role_name) VALUES 
('Admin'),
('Veterinarian'),
('Receptionist'),
('Owner');

-- Insert sample clinic
INSERT INTO Clinic (clinic_name, address, clinic_phone, clinic_email) VALUES 
('Demo Veterinary Clinic', '123 Main Street, Demo City', '(555) 123-4567', 'info@demo.healthpaws.co'),
('Happy Tails Veterinary Clinic', '456 Oak Avenue, Happy City', '(555) 987-6543', 'info@happytails.healthpaws.co'),
('Paw Care Animal Hospital', '789 Pine Street, Care Town', '(555) 456-7890', 'info@pawcare.healthpaws.co');

-- Insert sample vaccines
INSERT INTO Vaccine (vaccine_name, description) VALUES 
('Rabies', 'Core vaccine for rabies prevention'),
('DHPP', 'Core vaccine for distemper, hepatitis, parainfluenza, and parvovirus'),
('Bordetella', 'Vaccine for kennel cough prevention'),
('Lyme', 'Vaccine for Lyme disease prevention');
