<?php
class UserProfile extends GenericModelObject {
    
    const DEFAULT_THEME = 'dark';
    const DEFAULT_COLOR = '#00008B';
    
    private $formInput;
    private $userName;
    private $firstName;
    private $lastName;
    private $email;
    private $phone;
    private $gender;
    private $dob;
    private $country;
    private $picture;
    private $facebook;
    private $theme;
    private $accentColor;
    private $isProfilePublic;
    private $isPicturePublic;
    private $sendReminders;
    private $stayLoggedIn;
    
    public function __construct($formInput = null) {
        $this->formInput = $formInput;
        Messages::reset();
        $this->initialize();
    }
    
    public function getUserName() {
        return $this->userName;
    }
    
    public function getFirstName() {
        return $this->firstName;
    }
    
    public function getLastName() {
        return $this->lastName;
    }

    public function getEmail() {
        return $this->email;
    }
    
    public function getPhoneNumber() {
        return $this->phone;
    }
    
    public function getGender() {
        return $this->gender;
    }
    
    public function getDOB() {
        return $this->dob;
    }
    
    public function getCountry() {
        return $this->country;
    }
    
    public function getPicture() {
        return $this->picture;
    }
    
    public function getFacebook() {
        return $this->facebook;
    }
    
    public function getTheme() {
        return $this->theme;
    }
    
    public function getAccentColor() {
        return $this->accentColor;
    }
    
    public function isProfilePublic() {
        return $this->isProfilePublic;
    }
    
    public function isPicturePublic() {
        return $this->isPicturePublic;
    }
    
    public function isSendRemindersSet() {
        return $this->sendReminders;
    }
    
    public function isStayLoggedInSet() {
        return $this->stayLoggedIn;
    }
    
    // Returns data fields as an associative array
    public function getParameters() {
        $paramArray = array(
                "firstName" => $this->firstName,
                "lastName" => $this->lastName,
                "email" => $this->email,
                "phone" => $this->phone,
                "gender" => $this->gender,
                "dob" => $this->dob,
                "country" => $this->country,
                "picture" => $this->picture,
                "facebook" => $this->facebook,
                "theme" => $this->theme,
                "accentColor" => $this->accentColor,
                "isProfilePublic" => $this->isProfilePublic,
                "isPicturePublic" => $this->isPicturePublic,
                "sendReminders" => $this->sendReminders,
                "stayLoggedIn" => $this->stayLoggedIn,
                "userName" => $this->userName
        );
        
        return $paramArray;
    }
    
    public function __toString() {
        $str =
                "First name: [" . $this->firstName . "]\n" .
                "Last name: [" . $this->lastName . "]\n" .
                "E-mail address: [" . $this->email . "]\n" .
                "Phone number: [" . $this->phone . "]\n" .
                "Gender: [" . $this->gender . "]\n" .
                "Date of birth: [" . $this->dob . "]\n" .
                "Country: [" . $this->country . "]\n" .
                "Picture: [" . $this->picture . "]\n" .
                "Facebook: [" . $this->facebook . "]\n" .
                "Theme: [" . $this->theme . "]\n" .
                "Accent color: [" . $this->accentColor . "]\n" .
                "Profile public: [" . (($this->isProfilePublic === true) ? "true" : "false") . "]\n" .
                "Picture public: [" . (($this->isPicturePublic === true) ? "true" : "false") . "]\n" .
                "Send reminders: [" . (($this->sendReminders === true) ? "true" : "false") . "]\n" .
                "Stay logged in: [" . (($this->stayLoggedIn === true) ? "true" : "false") . "]\n";
        
        return $str;
    }
    
    protected function initialize() {
        $this->errorCount = 0;
        $this->errors = array();
        
        if (is_null($this->formInput)) {
            $this->firstName = "";
            $this->lastName = "";
            $this->email = "";
            $this->phone = "";
            $this->gender = "";
            $this->dob = "";
            $this->country = "";
            $this->picture = "";
            $this->facebook = "";
            $this->theme = self::DEFAULT_THEME;
            $this->accentColor = self::DEFAULT_COLOR;
            $this->isProfilePublic = false;
            $this->isPicturePublic = false;
            $this->sendReminders = false;
            $this->stayLoggedIn = false;
        }
        else {
            $this->validateFirstName();
            $this->validateLastName();
            $this->validateEmail();
            $this->validatePhone();
            $this->validateGender();
            $this->validateDOB();
            $this->validateCountry();
            $this->validatePicture();
            $this->validateFacebook();
            $this->validateTheme();
            $this->validateAccentColor();
            $this->validateIsProfilePublic();
            $this->validateIsPicturePublic();
            $this->validateSendReminders();
            $this->validateStayLoggedIn();
            $this->validateUserName();
        }
    }
    
    private function validateUserName() {
        $this->userName = $this->extractForm($this->formInput, "userName");
        if (empty($this->userName)) {
            $this->setError("userName", "USER_NAME_EMPTY");
            return;
        }
    
        if (strlen($this->userName) > 15) {
            $this->setError("userName", "USER_NAME_TOO_LONG");
            return;
        }
    
        $options = array("options" => array("regexp" => "/^[a-zA-Z0-9_-]+$/"));
        if (!filter_var($this->userName, FILTER_VALIDATE_REGEXP, $options)) {
            $this->setError("userName", "USER_NAME_HAS_INVALID_CHARS");
            return;
        }
    }
    
    private function validateFirstName() {
        $this->firstName = $this->extractForm($this->formInput, "firstName");
        if (empty($this->firstName)) {
            return;
        }
        
        if (strlen($this->firstName) > 30) {
            $this->setError("firstName", "FIRST_NAME_TOO_LONG");
            return;
        }
        
        $options = array("options" => array("regexp" => "/^[a-zA-Z ']+$/"));
        if (!filter_var($this->firstName, FILTER_VALIDATE_REGEXP, $options)) {
            $this->setError("firstName", "FIRST_NAME_HAS_INVALID_CHARS");
            return;
        }
    }
    
    private function validateLastName() {
        $this->lastName = $this->extractForm($this->formInput, "lastName");
        if (empty($this->lastName)) {
            return;
        }
        
        if (strlen($this->lastName) > 30) {
            $this->setError("lastName", "LAST_NAME_TOO_LONG");
            return;
        }
        
        $options = array("options" => array("regexp" => "/^[a-zA-Z ']+$/"));
        if (!filter_var($this->lastName, FILTER_VALIDATE_REGEXP, $options)) {
            $this->setError("lastName", "LAST_NAME_HAS_INVALID_CHARS");
            return;
        }
    }
    
    private function validateEmail() {
        $this->email = $this->extractForm($this->formInput, "email");
        if (empty($this->email)) {
            return;
        }
        
        if (strlen($this->email) > 30) {
            $this->setError("email", "EMAIL_TOO_LONG");
            return;
        }
        
        $options = array("options" => array("regexp" => "/^[A-Za-z0-9](([_\.\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([\.\-]?[a-zA-Z0-9]+)*)\.([A-Za-z]{2,})$/"));
        if (!filter_var($this->email, FILTER_VALIDATE_REGEXP, $options)) {
            $this->setError("email", "EMAIL_INVALID");
            return;
        }
    }
    
    private function validatePhone() {
        $this->phone = $this->extractForm($this->formInput, "phone");
        if (empty($this->phone)) {
            return;
        }
        
        if (strlen($this->phone) > 15) {
            $this->setError("phone", "PHONE_TOO_LONG");
            return;
        }
        
        $options = array("options" => array("regexp" => "/^(1\s*[-\/\.]?)?(\((\d{3})\)|(\d{3}))\s*[-\/\.]?\s*(\d{3})\s*[-\/\.]?\s*(\d{4})\s*(([xX]|[eE][xX][tT])\.?\s*(\d+))*$/"));
        if (!filter_var($this->phone, FILTER_VALIDATE_REGEXP, $options)) {
            $this->setError("phone", "PHONE_INVALID");
            return;
        }
    }
    
    private function validateGender() {
        $this->gender = $this->extractForm($this->formInput, "gender");
        if (empty($this->gender)) {
            return;
        }
        
        $options = array("options" => array("regexp" => "/^(male|female)$/i"));
        if (!filter_var($this->gender, FILTER_VALIDATE_REGEXP, $options)) {
            $this->setError("gender", "GENDER_INVALID");
            return;
        }
    }
    
    private function validateDOB() {
        $this->dob = $this->extractForm($this->formInput, "dob");
        if (empty($this->dob)) {
            return;
        }
        
        $options = array("options" => array("regexp" => "/^((\d{4}[\/-]\d\d[\/-]\d\d)|(\d\d[\/-]\d\d[\/-]\d{4}))$/"));
        if (!filter_var($this->dob, FILTER_VALIDATE_REGEXP, $options)) {
            $this->setError("dob", "DOB_INVALID");
            return;
        }
    }
    
    private function validateCountry() {
        $this->country = $this->extractForm($this->formInput, "country");
        if (empty($this->country)) {
            return;
        }
    }
    
    private function validatePicture() {
        $this->picture = $this->extractForm($this->formInput, "picture");
    }
    
    private function validateFacebook() {
        $this->facebook = $this->extractForm($this->formInput, "facebook");
        if (empty($this->facebook)) {
            return;
        }
        
        $options = array("options" => array("regexp" => "/((http|https):\/\/)?(www\.)?facebook\.com\/.+/"));
        if (!filter_var($this->facebook, FILTER_VALIDATE_REGEXP, $options)) {
            $this->setError("facebook", "FACEBOOK_INVALID");
            return;
        }
    }
    
    private function validateTheme() {
        $this->theme = $this->extractForm($this->formInput, "theme");
        if (empty($this->theme)) {
            $this->theme = self::DEFAULT_THEME;
            return;
        }
        
        $options = array("options" => array("regexp" => "/^(dark|light)$/i"));
        if (!filter_var($this->theme, FILTER_VALIDATE_REGEXP, $options)) {
            $this->setError("theme", "THEME_INVALID");
            return;
        }
    }
    
    private function validateAccentColor() {
        $this->accentColor = $this->extractForm($this->formInput, "accentColor");
        if (empty($this->accentColor)) {
            $this->accentColor = self::DEFAULT_COLOR;
            return;
        }
        
        $options = array("options" => array("regexp" => "/^#[a-fA-F0-9]{6}$/"));
        if (!filter_var($this->accentColor, FILTER_VALIDATE_REGEXP, $options)) {
            $this->setError("accentColor", "ACCENT_COLOR_INVALID");
            return;
        }
    }
    
    private function validateIsProfilePublic() {
        $value = $this->extractForm($this->formInput, "isProfilePublic");
        $this->isProfilePublic = ($value) ? true : false;
    }
    
    private function validateIsPicturePublic() {
        $value = $this->extractForm($this->formInput, "isPicturePublic");
        $this->isPicturePublic = ($value) ? true : false;
    }
    
    private function validateSendReminders() {
        $value = $this->extractForm($this->formInput, "sendReminders");
        $this->sendReminders = ($value) ? true : false;
    }
    
    private function validateStayLoggedIn() {
        $value = $this->extractForm($this->formInput, "stayLoggedIn");
        $this->stayLoggedIn = ($value) ? true : false;
    }
}
?>