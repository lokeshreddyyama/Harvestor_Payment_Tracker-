package com.harvester.controller;

import com.harvester.dto.ApiResponse;
import com.harvester.dto.StatsDto;
import com.harvester.security.CustomUserDetails;
import com.harvester.service.EntryService;
import org.springframework.security.core.annotation.AuthenticationPrincipal;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;

import java.util.Map;

@RestController
@RequestMapping("/api")
public class StatsController {

    private final EntryService entryService;

    public StatsController(EntryService entryService) {
        this.entryService = entryService;
    }

    @GetMapping("/public/health")
    public ApiResponse<Map<String, String>> health() {
        return ApiResponse.success(Map.of("status", "ok", "db", "harvester"));
    }

    @GetMapping("/stats")
    public ApiResponse<StatsDto> getStats(@AuthenticationPrincipal CustomUserDetails userDetails) {
        try {
            StatsDto stats = entryService.getStats(userDetails.getUser().getId());
            return ApiResponse.success(stats);
        } catch (Exception e) {
            return ApiResponse.error(e.getMessage());
        }
    }
}
