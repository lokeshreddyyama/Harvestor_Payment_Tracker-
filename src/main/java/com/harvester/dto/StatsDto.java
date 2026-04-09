package com.harvester.dto;

import com.harvester.model.HarvesterEntry;
import lombok.Builder;
import lombok.Data;

import java.util.List;

@Data
@Builder
public class StatsDto {
    private OverallDto overall;
    private TodayDto today;
    private List<HarvesterEntry> pending;

    @Data
    @Builder
    public static class OverallDto {
        private long total_entries;
        private Double total_amount;
        private Double total_collected;
        private Double total_balance;
        private Double total_acres;
        private Double total_fuel_cost;
        private long paid_count;
    }

    @Data
    @Builder
    public static class TodayDto {
        private long entry_count;
        private Double acres;
        private Double collected;
        private Double fuelCost;
    }
}
