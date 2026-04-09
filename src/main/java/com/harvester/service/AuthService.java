package com.harvester.service;

import com.harvester.dto.AuthResponse;
import com.harvester.dto.LoginRequest;
import com.harvester.dto.RegisterRequest;
import com.harvester.model.User;
import com.harvester.repository.UserRepository;
import com.harvester.security.JwtUtil;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.stereotype.Service;

import java.time.LocalDateTime;

@Service
public class AuthService {

    private final UserRepository userRepository;
    private final PasswordEncoder passwordEncoder;
    private final JwtUtil jwtUtil;

    public AuthService(UserRepository userRepository, PasswordEncoder passwordEncoder, JwtUtil jwtUtil) {
        this.userRepository = userRepository;
        this.passwordEncoder = passwordEncoder;
        this.jwtUtil = jwtUtil;
    }

    public boolean checkUsernameAvailable(String username) {
        return !userRepository.existsByUsername(username);
    }

    public void register(RegisterRequest request) {
        if (userRepository.existsByUsername(request.getUsername())) {
            throw new RuntimeException("Username already taken. Please choose another.");
        }

        User user = new User();
        user.setName(request.getName());
        user.setUsername(request.getUsername());
        user.setPhone(request.getPhone());
        user.setVehicle(request.getVehicle() != null ? request.getVehicle().toUpperCase() : null);
        user.setPassword(passwordEncoder.encode(request.getPassword()));
        
        userRepository.save(user);
    }

    public AuthResponse login(LoginRequest request) {
        User user = userRepository.findByUsername(request.getUsername())
                .orElseThrow(() -> new RuntimeException("Invalid username or password"));

        if (!passwordEncoder.matches(request.getPassword(), user.getPassword())) {
            throw new RuntimeException("Invalid username or password");
        }

        String token = jwtUtil.generateToken(user.getUsername());
        
        // Update user token and expr if required by old logic, though JWT solves this statelessly
        user.setToken(token);
        user.setTokenExp(LocalDateTime.now().plusHours(24));
        userRepository.save(user);

        return AuthResponse.builder()
                .token(token)
                .user(AuthResponse.UserDto.builder()
                        .id(user.getId())
                        .name(user.getName())
                        .username(user.getUsername())
                        .phone(user.getPhone())
                        .vehicle(user.getVehicle())
                        .build())
                .build();
    }

    public void logout(Long userId) {
        userRepository.findById(userId).ifPresent(user -> {
            user.setToken(null);
            user.setTokenExp(null);
            userRepository.save(user);
        });
    }

    public AuthResponse.UserDto getProfile(Long userId) {
        User user = userRepository.findById(userId)
                .orElseThrow(() -> new RuntimeException("User not found"));
        return AuthResponse.UserDto.builder()
                .id(user.getId())
                .name(user.getName())
                .username(user.getUsername())
                .phone(user.getPhone())
                .vehicle(user.getVehicle())
                .build();
    }
}
