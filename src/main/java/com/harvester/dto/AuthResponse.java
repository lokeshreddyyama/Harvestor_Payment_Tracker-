package com.harvester.dto;

import lombok.Builder;
import lombok.Data;

@Data
@Builder
public class AuthResponse {
    private String token;
    private UserDto user;

    @Data
    @Builder
    public static class UserDto {
        private Long id;
        private String name;
        private String username;
        private String phone;
        private String vehicle;
    }
}
