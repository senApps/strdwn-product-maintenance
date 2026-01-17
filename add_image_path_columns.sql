-- Add image_path column to Fighters table
ALTER TABLE Fighters ADD COLUMN IF NOT EXISTS image_path VARCHAR(255) DEFAULT NULL;

-- Add image_path column to Events table  
ALTER TABLE Events ADD COLUMN IF NOT EXISTS image_path VARCHAR(255) DEFAULT NULL;
