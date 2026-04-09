package com.harvester.service;

import com.harvester.model.User;
import com.harvester.repository.UserRepository;
import lombok.RequiredArgsConstructor;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.stereotype.Service;

import java.time.LocalDateTime;
import java.util.Optional;

@Service
@RequiredArgsConstructor
public class UserService {

    private final UserRepository userRepository;
    private final PasswordEncoder passwordEncoder;

    public Optional<User> findByUsername(String username) {
        return userRepository.findByUsername(username);
    }

    public Optional<User> findByToken(String token) {
        return userRepository.findByToken(token);
    }

    public boolean existsByUsername(String username) {
        return userRepository.existsByUsername(username);
    }

    public User save(User user) {
        return userRepository.save(user);
    }

    public User register(String name, String username, String phone, String vehicle, String password) {
        if (existsByUsername(username)) {
            throw new RuntimeException("Username already taken");
        }

        User user = new User();
        user.setName(name);
        user.setUsername(username);
        user.setPhone(phone);
        user.setVehicle(vehicle.toUpperCase());
        user.setPassword(passwordEncoder.encode(password));

        return save(user);
    }

    public User authenticate(String username, String password) {
        User user = findByUsername(username)
                .orElseThrow(() -> new RuntimeException("Invalid username or password"));

        if (!passwordEncoder.matches(password, user.getPassword())) {
            throw new RuntimeException("Invalid username or password");
        }

        return user;
    }

    public void updateToken(User user, String token, LocalDateTime expiry) {
        user.setToken(token);
        user.setTokenExp(expiry);
        save(user);
    }

    public void clearToken(User user) {
        user.setToken(null);
        user.setTokenExp(null);
        save(user);
    }

    public Optional<User> validateToken(String token) {
        return findByToken(token)
                .filter(user -> user.getTokenExp() != null && user.getTokenExp().isAfter(LocalDateTime.now()));
    }
}