ALTER TABLE business 
MODIFY COLUMN business_status ENUM('Approved','Rejected','Pending Approval','On Hold') 
NOT NULL DEFAULT 'Pending Approval';