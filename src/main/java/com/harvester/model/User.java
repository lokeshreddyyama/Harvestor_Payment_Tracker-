package com.harvester.model;

import jakarta.persistence.*;
import lombok.Data;
import lombok.Getter;
import lombok.Setter;
import org.hibernate.annotations.CreationTimestamp;

import java.time.LocalDateTime;

@Entity
@Table(name = "ht_users")
@Getter
@Setter
@Data
public class User {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @Column(nullable = false, length = 150)
    private String name;

    @Column(nullable = false, unique = true, length = 80)
    private String username;

    @Column(length = 20)
    private String phone;

    @Column(length = 50)
    private String vehicle;

    @Column(nullable = false)
    private String password;

    @Column(length = 255)
    private String token;

    @Column(name = "token_exp")
    private LocalDateTime tokenExp;

    @CreationTimestamp
    @Column(name = "created_at", updatable = false)
    private LocalDateTime createdAt;
}
