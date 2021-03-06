/* This install.sql is similar to one provided however provides extra sequences, view, indexes, and content 
 * for when starting to website for the first time.
 *
 *  File name:  setup.sql
 *  Function:   to create the initial database schema for the CMPUT 391 project,
 *              Winter Term, 2014
 *  Author:     Prof. Li-Yan Yuan
 */
DROP TABLE family_doctor;
DROP TABLE pacs_images;
DROP TABLE radiology_record;
DROP TABLE users;
DROP TABLE persons;
DROP SEQUENCE seq_persons_id;
DROP SEQUENCE seq_radiologyrecord_id;

DROP INDEX rad_rec_diagnosis;
DROP INDEX rad_rec_description;
DROP INDEX rad_rec_test_type;
DROP INDEX person_first_name;
DROP INDEX person_last_name;
DROP VIEW g_records;

/*
 *  To store the personal information
 */
CREATE TABLE persons (
   person_id int,
   first_name varchar(24),
   last_name  varchar(24),
   address    varchar(128),
   email      varchar(128),
   phone      char(10),
   PRIMARY KEY(person_id),
   UNIQUE (email)
);

/*
 * Creates sequences to increment tables
 */
CREATE SEQUENCE seq_persons_id START WITH 2 INCREMENT BY 1;
CREATE SEQUENCE seq_radiologyrecord_id START WITH 1 INCREMENT BY 1;

/*
 *  To store the log-in information
 *  Note that a person may have been assigned different user_name(s), depending
 *  on his/her role in the log-in  
 */
CREATE TABLE users (
   user_name varchar(24),
   password  varchar(24),
   class     char(1),
   person_id int,
   date_registered date,
   CHECK (class in ('a','p','d','r')),
   PRIMARY KEY(user_name),
   FOREIGN KEY (person_id) REFERENCES persons
);

/*
 *  to indicate who is whose family doctor.
 */
CREATE TABLE family_doctor (
   doctor_id    int,
   patient_id   int,
   FOREIGN KEY(doctor_id) REFERENCES persons,
   FOREIGN KEY(patient_id) REFERENCES persons,
   PRIMARY KEY(doctor_id,patient_id)
);

/*
 *  to store the radiology records
 */
CREATE TABLE radiology_record (
   record_id   int,
   patient_id  int,
   doctor_id   int,
   radiologist_id int,
   test_type   varchar(24),
   prescribing_date date,
   test_date    date,
   diagnosis    varchar(128),
   description   varchar(1024),
   PRIMARY KEY(record_id),
   FOREIGN KEY(patient_id) REFERENCES persons,
   FOREIGN KEY(doctor_id) REFERENCES  persons,
   FOREIGN KEY(radiologist_id) REFERENCES  persons
);

/*
 *  to store the pacs images
 */
CREATE TABLE pacs_images (
   record_id   int,
   image_id    int,
   thumbnail   blob,
   regular_size blob,
   full_size    blob,
   PRIMARY KEY(record_id,image_id),
   FOREIGN KEY(record_id) REFERENCES radiology_record
);

INSERT INTO persons VALUES(1,'admin','admin','admin','admin','admin');
INSERT INTO users VALUES('admin','admin','a',1,sysdate);

/*
* Creates indexes used in search module (CONTAINS function)
* This SQL is based off Troy Murphy, Tyler Wendlandt, and James Hodgson
*/
CREATE INDEX rad_rec_diagnosis on radiology_record(diagnosis)
INDEXTYPE IS CTXSYS.CONTEXT
parameters ('sync (on commit)');

CREATE INDEX rad_rec_description on radiology_record(description)
INDEXTYPE IS CTXSYS.CONTEXT
parameters ('sync (on commit)');

CREATE INDEX rad_rec_test_type on radiology_record(test_type)
INDEXTYPE IS CTXSYS.CONTEXT
parameters ('sync (on commit)');

CREATE INDEX person_first_name on persons(first_name)
INDEXTYPE is CTXSYS.CONTEXT
parameters ('sync (on commit)');

CREATE INDEX person_last_name on persons(last_name)
INDEXTYPE is CTXSYS.CONTEXT
parameters ('sync (on commit)');

CREATE VIEW g_records (patient_id,test_type,test_date,image_id)
AS SELECT r.patient_id, r.test_type, r.test_date, p.image_id
FROM radiology_record r, pacs_images p
WHERE r.record_id = p.record_id;

commit;
