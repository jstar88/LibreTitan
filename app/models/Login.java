package models;

import interfaces.Loginable;

public class Login implements Loginable {
        
        public String name;
        public String password;
        
        public String validate() {
            if(name == "" || password == "") {
                return "Invalid user or password";
            }
            return null;
        }
        public String getName()
        {
        	return name;
        }
        public String getPassword()
        {
        	return password;
        }
        
    }