package com.harvester.dto;

import lombok.Data;

@Data
public class RegisterRequest {
    private String name;
    private String username;
    private String phone;
    private String vehicle;
    private String password;
}
