package com.harvester.dto;

import lombok.Data;

import java.time.LocalDate;
import java.time.LocalDateTime;

@Data
public class EntryDto {
    private Long id;
    private Long userId;
    private String name;
    private String phone;
    private String address;
    private LocalDate date;
    private Double acres;
    private String crop;
    private Double rate;
    private Double amount;
    private Double collected;
    private Double balance;
    private String vehicle;
    private Double readStart;
    private Double readEnd;
    private Double fuelL;
    private Double fuelRate;
    private Double fuelCost;
    private String notes;
    private String photo;
    private Boolean paid;
    private LocalDateTime createdAt;
}

@Data
public class EntryRequest {
    private String name;
    private String phone;
    private String address;
    private String date;
    private Double acres;
    private String crop;
    private Double rate;
    private Double amount;
    private Double collected;
    private Double balance;
    private String vehicle;
    private Double readStart;
    private Double readEnd;
    private Double fuelL;
    private Double fuelRate;
    private Double fuelCost;
    private String notes;
    private String photo; // base64 data URL
}

@Data
public class EntryUpdateRequest {
    private String name;
    private String phone;
    private String address;
    private String date;
    private Double acres;
    private String crop;
    private Double rate;
    private Double amount;
    private Double collected;
    private Double balance;
    private String vehicle;
    private Double readStart;
    private Double readEnd;
    private Double fuelL;
    private Double fuelCost;
    private String notes;
}

@Data
public class StatsDto {
    private Long totalEntries;
    private Double totalAmount;
    private Double totalCollected;
    private Double totalBalance;
    private Double totalAcres;
    private Double totalFuelCost;
    private Long paidCount;
}

@Data
public class TodayStatsDto {
    private Long entryCount;
    private Double acres;
    private Double collected;
    private Double fuelCost;
}

@Data
public class OverallStatsDto {
    private StatsDto overall;
    private TodayStatsDto today;
    private java.util.List<EntryDto> pending;
}