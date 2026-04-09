package com.harvester.service;

import com.harvester.dto.StatsDto;
import com.harvester.model.HarvesterEntry;
import com.harvester.repository.EntryRepository;
import org.springframework.stereotype.Service;

import java.time.LocalDate;
import java.util.List;

@Service
public class EntryService {

    private final EntryRepository entryRepository;
    private final FileStorageService fileStorageService;

    public EntryService(EntryRepository entryRepository, FileStorageService fileStorageService) {
        this.entryRepository = entryRepository;
        this.fileStorageService = fileStorageService;
    }

    public List<HarvesterEntry> getRecords(Long userId, String search, String status) {
        Boolean paid = null;
        if ("paid".equalsIgnoreCase(status)) {
            paid = true;
        } else if ("pending".equalsIgnoreCase(status)) {
            paid = false;
        }

        if (search != null && !search.isEmpty()) {
            if (paid != null) {
                return entryRepository.searchEntriesWithStatus(userId, search, paid);
            } else {
                return entryRepository.searchEntries(userId, search);
            }
        } else {
            if (paid != null) {
                return entryRepository.findByUserIdAndPaidOrderByDateDescIdDesc(userId, paid);
            } else {
                return entryRepository.findByUserIdOrderByDateDescIdDesc(userId);
            }
        }
    }

    public HarvesterEntry createEntry(Long userId, HarvesterEntry entry) {
        entry.setUserId(userId);
        
        if (entry.getDate() == null) {
            entry.setDate(LocalDate.now());
        }
        
        if (entry.getPhoto() != null && entry.getPhoto().startsWith("data:image")) {
            String fileName = fileStorageService.savePhoto(entry.getPhoto());
            entry.setPhoto(fileName);
        } else {
            entry.setPhoto(null);
        }
        
        if (entry.getVehicle() != null) {
            entry.setVehicle(entry.getVehicle().toUpperCase());
        }
        
        entry.setPaid(false);
        return entryRepository.save(entry);
    }

    public HarvesterEntry updateEntry(Long id, Long userId, HarvesterEntry updateData) {
        HarvesterEntry existing = entryRepository.findByIdAndUserId(id, userId)
                .orElseThrow(() -> new RuntimeException("Entry not found or access denied"));
        
        existing.setName(updateData.getName());
        existing.setPhone(updateData.getPhone());
        existing.setAddress(updateData.getAddress());
        existing.setDate(updateData.getDate() != null ? updateData.getDate() : LocalDate.now());
        existing.setAcres(updateData.getAcres());
        existing.setCrop(updateData.getCrop());
        existing.setRate(updateData.getRate());
        existing.setAmount(updateData.getAmount());
        existing.setCollected(updateData.getCollected());
        existing.setBalance(updateData.getBalance());
        existing.setVehicle(updateData.getVehicle() != null ? updateData.getVehicle().toUpperCase() : null);
        existing.setReadStart(updateData.getReadStart());
        existing.setReadEnd(updateData.getReadEnd());
        existing.setFuelL(updateData.getFuelL());
        existing.setFuelCost(updateData.getFuelCost());
        existing.setNotes(updateData.getNotes());
        
        return entryRepository.save(existing);
    }

    public void deleteEntry(Long id, Long userId) {
        HarvesterEntry existing = entryRepository.findByIdAndUserId(id, userId)
                .orElseThrow(() -> new RuntimeException("Entry not found or access denied"));
        
        if (existing.getPhoto() != null) {
            fileStorageService.deletePhoto(existing.getPhoto());
        }
        
        entryRepository.delete(existing);
    }

    public HarvesterEntry togglePaid(Long id, Long userId) {
        HarvesterEntry existing = entryRepository.findByIdAndUserId(id, userId)
                .orElseThrow(() -> new RuntimeException("Entry not found or access denied"));
        
        existing.setPaid(!existing.getPaid());
        return entryRepository.save(existing);
    }

    public StatsDto getStats(Long userId) {
        LocalDate today = LocalDate.now();

        StatsDto.OverallDto overall = StatsDto.OverallDto.builder()
                .total_entries(entryRepository.countByUserId(userId))
                .total_amount(entryRepository.sumAmountByUserId(userId))
                .total_collected(entryRepository.sumCollectedByUserId(userId))
                .total_balance(entryRepository.sumBalanceByUserId(userId))
                .total_acres(entryRepository.sumAcresByUserId(userId))
                .total_fuel_cost(entryRepository.sumFuelCostByUserId(userId))
                .paid_count(entryRepository.countPaidByUserId(userId))
                .build();

        StatsDto.TodayDto todayDto = StatsDto.TodayDto.builder()
                .entry_count(entryRepository.countByUserIdAndDate(userId, today))
                .acres(entryRepository.sumAcresByUserIdAndDate(userId, today))
                .collected(entryRepository.sumCollectedByUserIdAndDate(userId, today))
                .fuelCost(entryRepository.sumFuelCostByUserIdAndDate(userId, today))
                .build();

        List<HarvesterEntry> pending = entryRepository.findTop20ByUserIdAndPaidFalseAndBalanceGreaterThanOrderByBalanceDesc(userId, 0.0);

        return StatsDto.builder().overall(overall).today(todayDto).pending(pending).build();
    }
    
    public List<HarvesterEntry> getAllForExport(Long userId) {
        return entryRepository.findByUserIdOrderByDateDesc(userId);
    }
}