package com.harvester.controller;

import com.harvester.dto.ApiResponse;
import com.harvester.dto.AuthResponse;
import com.harvester.dto.LoginRequest;
import com.harvester.dto.RegisterRequest;
import com.harvester.security.CustomUserDetails;
import com.harvester.service.AuthService;
import org.springframework.security.core.annotation.AuthenticationPrincipal;
import org.springframework.web.bind.annotation.*;

import java.util.Map;

@RestController
@RequestMapping("/api")
public class AuthController {

    private final AuthService authService;

    public AuthController(AuthService authService) {
        this.authService = authService;
    }

    @GetMapping("/public/check_user")
    public ApiResponse<Map<String, Boolean>> checkUser(@RequestParam String username) {
        if (username == null || username.trim().isEmpty()) {
            return ApiResponse.success(Map.of("available", false));
        }
        boolean available = authService.checkUsernameAvailable(username);
        return ApiResponse.success(Map.of("available", available));
    }

    @PostMapping("/auth/register")
    public ApiResponse<Map<String, String>> register(@RequestBody RegisterRequest request) {
        try {
            authService.register(request);
            return ApiResponse.success(Map.of("message", "Account created successfully"));
        } catch (Exception e) {
            return ApiResponse.error(e.getMessage());
        }
    }

    @PostMapping("/auth/login")
    public ApiResponse<AuthResponse> login(@RequestBody LoginRequest request) {
        try {
            AuthResponse response = authService.login(request);
            return ApiResponse.success(response);
        } catch (Exception e) {
            return ApiResponse.error(e.getMessage());
        }
    }

    @PostMapping("/logout")
    public ApiResponse<Map<String, String>> logout(@AuthenticationPrincipal CustomUserDetails userDetails) {
        if (userDetails != null) {
            authService.logout(userDetails.getUser().getId());
        }
        return ApiResponse.success(Map.of("message", "Logged out"));
    }

    @GetMapping("/profile")
    public ApiResponse<AuthResponse.UserDto> profile(@AuthenticationPrincipal CustomUserDetails userDetails) {
        try {
            AuthResponse.UserDto profile = authService.getProfile(userDetails.getUser().getId());
            return ApiResponse.success(profile);
        } catch (Exception e) {
            return ApiResponse.error(e.getMessage());
        }
    }
}