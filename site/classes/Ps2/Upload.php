<?php
namespace Ps2;

class Upload {
    // beginning each variable name with an underscore, depicts it as a protected vairable
    protected $_uploaded = array();
    protected $_destination;
    protected $_max = 51200;
    protected $_messages = array();
    protected $_permitted = array('image/gif',
                                  'image/jpg',
                                  'image/pjpeg',
                                  'image/png');
    protected $_renamed = false;
    
    public function __construct($path) {
        // is the folder path a valid directory, and is that folder able to be written to
        if (!is_dir($path) || !is_writable($path)) {
            throw new Exception("$path must be a valid, writable directory.");
        }
        $this->_destination = $path;
        $this->_uploaded = $_FILES;
    }
    
    
    
    public function move($overwrite = false) {
        $field = current($this->_uploaded);
        
        // Check for multiple uploads by determining if it is an array
        if (is_array($field['name'])) {
            
            // Remember that 'name' is the array containing the multiple upload files.
            // It contains a subarray for each attribute of all uploads. 
            foreach ($field['name'] as $number => $filename) {
                // process multiple upload
                $this->_renamed = false;
                $this->processFile($field['name'][$number], 
                                    $field['error'][$number], 
                                    $field['size'][$number], 
                                    $field['type'][$number], 
                                    $field['tmp_name'][$number], 
                                    $overwrite);
            }
        } else {
            $this->processFile($field['name'], $field['error'], $field['size'], $field['type'], $field['tmp_name'], $overwrite);
        }
        
        
        
    }
    
    
    
    protected function processFile($filename, $error, $size, $type, $tmp_name, $overwrite) {
        $OK = $this->checkError($filename, $error);
        if ($OK) {
            $sizeOK = $this->checkSize($filename, $size);
            $typeOK = $this->checkType($filename, $type);
            
            // If the file size is under the maxsize and the file type is a permitted MIME type
            if ($sizeOK && $typeOK) {
                // Determine name and whether it will overwrite a previous file, or if it needs to 
                $filename = $this->checkName($filename, $overwrite);
                // Move the uploaded temporary file to it's correct directory
                $success = move_uploaded_file($tmp_name, $this->_destination . $filename);
                
                // Did the file upload succsessfully
                if ($success) {
                    // Display success to user via message
                    $message = $filename . ' uploaded successfully';
                    
                    //If the file has to be renamed with underscores or by appending numbers
                    //Let the user be aware of the new filename
                    if ($this->_renamed) {
                        $message .= " and renamed $filename";
                    } 
                    $this->_messages[] = $message;
                } else {
                    $this->_messages[] = 'Could not upload ' . $filename;
                }
            }
        }
    }


    public function getMessages() {
        return $this->_messages;
    }
    
    
    
    protected function checkError($filename, $error) {
        switch ($error) {
            case 0:
                return true;
            case 1:
            case 2:
                $this->_messages[] = "$filename exceeds maximum size: " . $this->getMaxSize();
                return true;
            case 3:
                $this->_messages[] = "Error uploading $filename. Please try again.";
                return false;
            case 4:
                $this->_messages[] = 'No file selected.';
                return false;
            case 5:
                $this->_messages[] = 'System error uploading $filename. Contact webmaster.';
                return false;
        }
    }
    
    
    
    protected function checkSize($filename, $size) {
        if ($size == 0) {
            return false;
        } elseif ($size > $this->_max) {
            $this->_messages[] = "$filename exceeds maximum size: " . $this->getMaxSize();
            return false;
        } else {
            return true;
        }
    }
    
    
    
    protected function checkType($filename, $type) {
        if (empty($type)){
            return false;
        } elseif (!in_array($type, $this->_permitted)) {
            $this->_messages[] = "$filename is not a permitted type of file.";
            return false;
        } else {
            return true;
        }
    }
    
    
    
    public function getMaxSize() {
        return number_format($this->_max/1024, 1) . 'kB';
    }
    
    
    // Add new permitted document types by 1 or multiple doc types at once
    public function addPermittedTypes($types) {
        $types = (array) $types;
        $this->isValidMime($types);
        $this->_permitted = array_merge($this->_permitted, $types);
    }
    
    
    // Replace the entire list of permitted doc types
    public function setPermittedTypes($types) {
        $types = (array) $types;
        $this->isValidMime($types);
        $this->_permitted = $types;
    }
    
    
    
    protected function isValidMime($types) {
        // A list of other valid MIME types to be uploaded
        $alsoValid = array('image/tiff',
                           'application/pdf', 
                           'text/plain',
                           'text/rtf');
        // Combine the two arrays into one list
        $valid = array_merge($this->_permitted, $alsoValid);
        foreach ($types as $type) {
            if (!in_array($type, $valid)) {
                throw new Exception("$type is not a permitted MIME type");
            }
        }
    }
    
    public function setMaxSize($num) {
        if (!is_numeric($num)) {
            throw new Exception("Maximum size must be a number");
        }
        $this->_max = (int) $num;
    }
    
    
    protected function checkName($name, $overwrite) {
        $nospaces = str_replace(' ', '_', $name);
        
        if ($nospaces != $name) {
            $this->_renamed = true;
        }
        if (!$overwrite) {
            // rename the file if it already exists
            
            // returns array of all files or folders
            $existing = scandir($this->_destination);
            // Determine if the document name with underscores is already in the directory
            //if not included, then save it as is with out appending a number to the end
            //if the underscore name is in the list, add a number
            if(in_array($nospaces, $existing)) {
                // Find the position of the . in the documents name, save it as an indexed number
                // The purpose is to add a number after the base name, but before the '.' and extension
                $dot = strrpos($nospaces, '.');
                if ($dot) {
                    // Search string ($string to search, starting position, last & exclude this character)
                    $base = substr($nospaces, 0, $dot);
                    // Find the extension by starting the search including the period and going to the end
                    $extension = substr($nospaces, $dot);
                } else {
                    $base = $nospaces;
                    $extension = '';
                }
                // Start the appended number at 1
                $i = 1;
                
                // run the loop while there is a file in the directory that matches the $base . counter . $extension
                do {
                    $nospaces = $base . '_' . $i++ . $extension;
                } while (in_array($nospaces, $existing));
                $this->_renamed = true;
            }
        }
        return $nospaces;
    }
}