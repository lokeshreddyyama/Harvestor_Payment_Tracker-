package com.harvester.dto;

import lombok.Data;

import java.time.LocalDate;

@Data
public class LoginRequest {
    private String username;
    private String password;
}

@Data
public class RegisterRequest {
    private String name;
    private String username;
    private String phone;
    private String vehicle;
    private String password;
}

@Data
public class LoginResponse {
    private String token;
    private UserDto user;
}

@Data
public class UserDto {
    private Long id;
    private String name;
    private String username;
    private String phone;
    private String vehicle;
}

@Data
public class ApiResponse<T> {
    private boolean success;
    private T data;
    private String message;
    private String error;

    public static <T> ApiResponse<T> success(T data) {
        ApiResponse<T> response = new ApiResponse<>();
        response.setSuccess(true);
        response.setData(data);
        return response;
    }

    public static <T> ApiResponse<T> success(T data, String message) {
        ApiResponse<T> response = new ApiResponse<>();
        response.setSuccess(true);
        response.setData(data);
        response.setMessage(message);
        return response;
    }

    public static <T> ApiResponse<T> error(String error) {
        ApiResponse<T> response = new ApiResponse<>();
        response.setSuccess(false);
        response.setError(error);
        return response;
    }
}