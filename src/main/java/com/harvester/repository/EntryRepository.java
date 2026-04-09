package com.harvester.repository;

import com.harvester.model.HarvesterEntry;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.stereotype.Repository;

import java.time.LocalDate;
import java.util.List;
import java.util.Optional;

@Repository
public interface EntryRepository extends JpaRepository<HarvesterEntry, Long> {
    
    List<HarvesterEntry> findByUserIdOrderByDateDescIdDesc(Long userId);
    
    // Status can be accessed by matching boolean paid
    List<HarvesterEntry> findByUserIdAndPaidOrderByDateDescIdDesc(Long userId, Boolean paid);

    // Basic search functionality matching PHP API
    @Query("SELECT e FROM HarvesterEntry e WHERE e.userId = :userId AND " +
           "(e.name LIKE %:search% OR e.phone LIKE %:search% OR e.address LIKE %:search%) " +
           "ORDER BY e.date DESC, e.id DESC")
    List<HarvesterEntry> searchEntries(Long userId, String search);
    
    // Search with status
    @Query("SELECT e FROM HarvesterEntry e WHERE e.userId = :userId AND e.paid = :paid AND " +
           "(e.name LIKE %:search% OR e.phone LIKE %:search% OR e.address LIKE %:search%) " +
           "ORDER BY e.date DESC, e.id DESC")
    List<HarvesterEntry> searchEntriesWithStatus(Long userId, String search, Boolean paid);

    Optional<HarvesterEntry> findByIdAndUserId(Long id, Long userId);

    @Query("SELECT COUNT(e) FROM HarvesterEntry e WHERE e.userId = :userId")
    long countByUserId(Long userId);

    @Query("SELECT COALESCE(SUM(e.amount), 0) FROM HarvesterEntry e WHERE e.userId = :userId")
    Double sumAmountByUserId(Long userId);

    @Query("SELECT COALESCE(SUM(e.collected), 0) FROM HarvesterEntry e WHERE e.userId = :userId")
    Double sumCollectedByUserId(Long userId);

    @Query("SELECT COALESCE(SUM(e.balance), 0) FROM HarvesterEntry e WHERE e.userId = :userId")
    Double sumBalanceByUserId(Long userId);

    @Query("SELECT COALESCE(SUM(e.acres), 0) FROM HarvesterEntry e WHERE e.userId = :userId")
    Double sumAcresByUserId(Long userId);

    @Query("SELECT COALESCE(SUM(e.fuelCost), 0) FROM HarvesterEntry e WHERE e.userId = :userId")
    Double sumFuelCostByUserId(Long userId);

    @Query("SELECT COUNT(e) FROM HarvesterEntry e WHERE e.userId = :userId AND e.paid = true")
    long countPaidByUserId(Long userId);

    // Today stats
    @Query("SELECT COUNT(e) FROM HarvesterEntry e WHERE e.userId = :userId AND e.date = :today")
    long countByUserIdAndDate(Long userId, LocalDate today);
    
    @Query("SELECT COALESCE(SUM(e.acres), 0) FROM HarvesterEntry e WHERE e.userId = :userId AND e.date = :today")
    Double sumAcresByUserIdAndDate(Long userId, LocalDate today);

    @Query("SELECT COALESCE(SUM(e.collected), 0) FROM HarvesterEntry e WHERE e.userId = :userId AND e.date = :today")
    Double sumCollectedByUserIdAndDate(Long userId, LocalDate today);

    @Query("SELECT COALESCE(SUM(e.fuelCost), 0) FROM HarvesterEntry e WHERE e.userId = :userId AND e.date = :today")
    Double sumFuelCostByUserIdAndDate(Long userId, LocalDate today);

    List<HarvesterEntry> findTop20ByUserIdAndPaidFalseAndBalanceGreaterThanOrderByBalanceDesc(Long userId, Double balance);
    
    List<HarvesterEntry> findByUserIdOrderByDateDesc(Long userId);
}
