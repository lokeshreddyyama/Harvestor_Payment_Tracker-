package com.harvester.controller;

import com.harvester.dto.ApiResponse;
import com.harvester.model.HarvesterEntry;
import com.harvester.security.CustomUserDetails;
import com.harvester.service.EntryService;
import com.opencsv.CSVWriter;
import jakarta.servlet.http.HttpServletResponse;
import org.springframework.http.HttpHeaders;
import org.springframework.security.core.annotation.AuthenticationPrincipal;
import org.springframework.web.bind.annotation.*;

import java.io.IOException;
import java.io.PrintWriter;
import java.time.LocalDate;
import java.util.List;
import java.util.Map;

@RestController
@RequestMapping("/api")
public class EntryController {

    private final EntryService entryService;

    public EntryController(EntryService entryService) {
        this.entryService = entryService;
    }

    @GetMapping("/records")
    public ApiResponse<List<HarvesterEntry>> getRecords(
            @AuthenticationPrincipal CustomUserDetails userDetails,
            @RequestParam(required = false) String search,
            @RequestParam(required = false) String status) {
        try {
            List<HarvesterEntry> records = entryService.getRecords(userDetails.getUser().getId(), search, status);
            return ApiResponse.success(records);
        } catch (Exception e) {
            return ApiResponse.error(e.getMessage());
        }
    }

    @PostMapping("/records")
    public ApiResponse<HarvesterEntry> createRecord(
            @AuthenticationPrincipal CustomUserDetails userDetails,
            @RequestBody HarvesterEntry entry) {
        try {
            HarvesterEntry saved = entryService.createEntry(userDetails.getUser().getId(), entry);
            return ApiResponse.success(saved, "Entry saved");
        } catch (Exception e) {
            return ApiResponse.error("Insert failed: " + e.getMessage());
        }
    }

    @PutMapping("/record")
    public ApiResponse<HarvesterEntry> updateRecord(
            @AuthenticationPrincipal CustomUserDetails userDetails,
            @RequestParam Long id,
            @RequestBody HarvesterEntry entry) {
        try {
            HarvesterEntry updated = entryService.updateEntry(id, userDetails.getUser().getId(), entry);
            return ApiResponse.success(updated, "Entry updated");
        } catch (Exception e) {
            return ApiResponse.error("Update failed: " + e.getMessage());
        }
    }

    @DeleteMapping("/record")
    public ApiResponse<Void> deleteRecord(
            @AuthenticationPrincipal CustomUserDetails userDetails,
            @RequestParam Long id) {
        try {
            entryService.deleteEntry(id, userDetails.getUser().getId());
            return ApiResponse.success(null, "Entry deleted");
        } catch (Exception e) {
            return ApiResponse.error(e.getMessage());
        }
    }

    @PostMapping("/toggle")
    public ApiResponse<Map<String, Object>> togglePaid(
            @AuthenticationPrincipal CustomUserDetails userDetails,
            @RequestBody Map<String, Long> payload) {
        try {
            Long id = payload.get("id");
            if (id == null) {
                return ApiResponse.error("Invalid ID");
            }
            HarvesterEntry toggled = entryService.togglePaid(id, userDetails.getUser().getId());
            String message = toggled.getPaid() ? "✅ Marked as Paid" : "↩️ Marked as Pending";
            return ApiResponse.success(Map.of(
                    "id", id,
                    "paid", toggled.getPaid(),
                    "message", message
            ));
        } catch (Exception e) {
            return ApiResponse.error(e.getMessage());
        }
    }

    @GetMapping("/export")
    public void exportRecords(
            @AuthenticationPrincipal CustomUserDetails userDetails,
            HttpServletResponse response) throws IOException {
        
        String filename = "harvester_records_" + LocalDate.now() + ".csv";
        
        response.setContentType("text/csv; charset=utf-8");
        response.setHeader(HttpHeaders.CONTENT_DISPOSITION, "attachment; filename=\"" + filename + "\"");

        List<HarvesterEntry> records = entryService.getAllForExport(userDetails.getUser().getId());

        try (PrintWriter writer = response.getWriter();
             CSVWriter csvWriter = new CSVWriter(writer)) {

            String[] headerRecord = {"ID", "Name", "Phone", "Address", "Date", "Acres", "Crop", "Rate", "Amount",
                    "Collected", "Balance", "Vehicle", "Start KM", "End KM", "Fuel (L)", "Fuel Rate", "Fuel Cost",
                    "Notes", "Photo", "Paid", "Created At"};
            csvWriter.writeNext(headerRecord);

            for (HarvesterEntry r : records) {
                String[] data = {
                        String.valueOf(r.getId()),
                        r.getName() != null ? r.getName() : "",
                        r.getPhone() != null ? r.getPhone() : "",
                        r.getAddress() != null ? r.getAddress() : "",
                        r.getDate() != null ? r.getDate().toString() : "",
                        String.valueOf(r.getAcres()),
                        r.getCrop() != null ? r.getCrop() : "",
                        String.valueOf(r.getRate()),
                        String.valueOf(r.getAmount()),
                        String.valueOf(r.getCollected()),
                        String.valueOf(r.getBalance()),
                        r.getVehicle() != null ? r.getVehicle() : "",
                        String.valueOf(r.getReadStart()),
                        String.valueOf(r.getReadEnd()),
                        String.valueOf(r.getFuelL()),
                        String.valueOf(r.getFuelRate()),
                        String.valueOf(r.getFuelCost()),
                        r.getNotes() != null ? r.getNotes() : "",
                        r.getPhoto() != null ? "uploads/" + r.getPhoto() : "",
                        r.getPaid() ? "Yes" : "No",
                        r.getCreatedAt() != null ? r.getCreatedAt().toString() : ""
                };
                csvWriter.writeNext(data);
            }
        }
    }
}