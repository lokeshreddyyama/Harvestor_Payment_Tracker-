package com.harvester.model;

import jakarta.persistence.*;
import lombok.Data;
import lombok.Getter;
import lombok.Setter;
import org.hibernate.annotations.CreationTimestamp;

import java.time.LocalDate;
import java.time.LocalDateTime;

@Entity
@Table(name = "harvester_entries")
@Getter
@Setter
@Data
public class HarvesterEntry {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @Column(name = "user_id", nullable = false)
    private Long userId;

    @Column(nullable = false, length = 150)
    private String name;

    @Column(length = 20)
    private String phone;

    @Column(length = 255)
    private String address;

    @Column(name = "date")
    private LocalDate date;

    @Column(columnDefinition = "DECIMAL(8,2) DEFAULT 0")
    private Double acres = 0.0;

    @Column(length = 100)
    private String crop;

    @Column(columnDefinition = "DECIMAL(10,2) DEFAULT 0")
    private Double rate = 0.0;

    @Column(columnDefinition = "DECIMAL(10,2) DEFAULT 0")
    private Double amount = 0.0;

    @Column(columnDefinition = "DECIMAL(10,2) DEFAULT 0")
    private Double collected = 0.0;

    @Column(columnDefinition = "DECIMAL(10,2) DEFAULT 0")
    private Double balance = 0.0;

    @Column(length = 50)
    private String vehicle;

    @Column(name = "read_start", columnDefinition = "DECIMAL(10,2) DEFAULT 0")
    private Double readStart = 0.0;

    @Column(name = "read_end", columnDefinition = "DECIMAL(10,2) DEFAULT 0")
    private Double readEnd = 0.0;

    @Column(name = "fuel_l", columnDefinition = "DECIMAL(8,2) DEFAULT 0")
    private Double fuelL = 0.0;

    @Column(name = "fuel_rate", columnDefinition = "DECIMAL(8,2) DEFAULT 0")
    private Double fuelRate = 0.0;

    @Column(name = "fuel_cost", columnDefinition = "DECIMAL(10,2) DEFAULT 0")
    private Double fuelCost = 0.0;

    @Column(columnDefinition = "TEXT")
    private String notes;

    @Column(length = 255)
    private String photo;

    @Column(nullable = false, columnDefinition = "TINYINT(1) DEFAULT 0")
    private Boolean paid = false;

    @CreationTimestamp
    @Column(name = "created_at", updatable = false)
    private LocalDateTime createdAt;
}
