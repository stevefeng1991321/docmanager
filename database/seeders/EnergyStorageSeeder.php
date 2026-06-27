<?php

namespace Database\Seeders;

use App\Models\BasicKnowledgeTrend;
use App\Models\Category;
use Illuminate\Database\Seeder;

class EnergyStorageSeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::firstOrCreate(
            ['name' => 'Energy Storage'],
            ['slug' => 'energy-storage']
        );

        $entries = [
            [
                'title'   => 'Lithium-Ion Battery Fundamentals: Electrochemistry, Cell Construction, and Degradation',
                'summary' => 'How lithium-ion cells work at the electrochemical level, the role of each cell component, and the primary degradation mechanisms that limit cycle life.',
                'tags'    => ['lithium-ion', 'battery', 'electrochemistry', 'cathode', 'anode', 'cell degradation', 'energy storage'],
                'content' => <<<MD
# Lithium-Ion Battery Fundamentals: Electrochemistry, Cell Construction, and Degradation

Lithium-ion batteries (LIBs) are the dominant rechargeable energy storage technology for portable electronics, electric vehicles, and increasingly grid-scale applications. Understanding their electrochemistry is essential for selecting the right chemistry and managing cycle life.

## Operating Principle

A LIB cell converts chemical energy to electrical energy through reversible lithium intercalation reactions.

**Discharge** (lithium moves from anode to cathode through the electrolyte):
- Anode (negative): LiₓC₆ → C₆ + x Li⁺ + x e⁻
- Cathode (positive): Li₁₋ₓCoO₂ + x Li⁺ + x e⁻ → LiCoO₂

**Charge**: Reverse of above. Lithium is extracted from cathode and intercalated into anode.

Cell voltage: Determined by the difference in electrochemical potential (Fermi level) between cathode and anode. Typical full cell: 3.2–4.2 V.

## Cell Components

| Component | Material (typical) | Function |
|-----------|-------------------|---------|
| Cathode | LFP, NMC, NCA, LCO | Lithium host; determines voltage and capacity |
| Anode | Graphite (C₆) | Lithium host; determines fast-charge capability |
| Electrolyte | LiPF₆ in EC/DMC | Ionic conductor; electrical insulator |
| Separator | Polyethylene / polypropylene | Physical barrier; prevents short circuit; permits Li⁺ transport |
| Current collectors | Al (cathode), Cu (anode) | Electron conduction |

## Cathode Chemistry Comparison

| Chemistry | Voltage (V) | Specific Energy (Wh/kg) | Thermal Stability | Cycle Life | Cost |
|-----------|------------|------------------------|------------------|-----------|------|
| LFP (LiFePO₄) | 3.2 | 90–120 | Excellent | >2000 cycles | Low |
| NMC (LiNiMnCoO₂) | 3.7 | 150–220 | Moderate | 500–1500 cycles | Moderate |
| NCA (LiNiCoAlO₂) | 3.65 | 200–260 | Moderate | 500–1000 cycles | High |
| LCO (LiCoO₂) | 3.9 | 150–190 | Poor | 300–500 cycles | Very high |

## Solid Electrolyte Interphase (SEI)

During the first charge, electrolyte reduction at the anode surface forms an SEI layer:
- Passivates anode surface — prevents continuous electrolyte decomposition.
- Consumes lithium irreparably — causes the **first-cycle irreversible capacity loss** (5–20% of initial capacity).
- Composition: Li₂CO₃, LiF, organic lithium salts. Depends heavily on electrolyte additives (vinylene carbonate stabilises SEI).

## Degradation Mechanisms

1. **Lithium plating**: At high charge rates or low temperatures, Li⁺ deposits as metallic lithium on the anode instead of intercalating. Can form dendrites → internal short circuit → thermal runaway.
2. **SEI growth**: Slow continuous electrolyte reduction increases SEI thickness → rising internal resistance → capacity fade.
3. **Cathode structural degradation**: In NMC/NCA at high state of charge, Ni⁴⁺ is chemically aggressive — causes Ni migration, layered-to-spinel transformation.
4. **Particle cracking**: Volume change during charge/discharge (~10% for graphite) causes mechanical fatigue and particle isolation.
5. **Gas evolution**: Overcharge, overtemperature, or electrolyte decomposition produces CO₂, CO, and other gases → cell swelling.

## Thermal Runaway

Self-sustaining exothermic chain of reactions triggered by overcharge, external heat, or mechanical penetration:
- SEI decomposition: ~90–120 °C
- Electrolyte combustion: ~150–200 °C
- Cathode oxygen release (NCA/NMC): ~170–200 °C
- Separator melt: ~130–180 °C

Prevention: Battery Management System (BMS) with accurate SoC/SoT estimation; thermal management; cell-level pressure relief vents.
MD,
            ],

            [
                'title'   => 'Sodium-Ion Batteries: Technology, Advantages, and Commercialisation Status',
                'summary' => 'How sodium-ion batteries differ from lithium-ion, where they hold a genuine cost and resource advantage, and the current state of commercialisation as of 2024.',
                'tags'    => ['sodium-ion', 'battery', 'energy storage', 'hard carbon', 'Prussian blue', 'grid storage'],
                'content' => <<<MD
# Sodium-Ion Batteries: Technology, Advantages, and Commercialisation Status

Sodium-ion batteries (SIBs) operate on the same rocking-chair intercalation principle as lithium-ion, but substitute Na⁺ for Li⁺ ions. The motivation is abundant sodium (2.6% of Earth's crust vs 0.002% for lithium) and competitive raw material costs.

## Electrochemistry

**Key difference from LIBs**: Na⁺ is larger (1.02 Å vs 0.76 Å for Li⁺) and heavier (23 g/mol vs 6.9 g/mol). This limits energy density and requires host materials with wider ion channels.

**Critical advantage**: No copper current collector required at the anode — Na does not alloy with aluminium at low voltages (Li does), so both electrodes can use aluminium foil → cost saving and weight reduction.

## Cathode Materials

| Class | Examples | Voltage vs Na⁺/Na | Capacity (mAh/g) | Notes |
|-------|---------|-------------------|-----------------|-------|
| Layered oxide | P2-Na₂/₃MnO₂, O3-NaFeO₂ | 2.5–4.0 V | 100–200 | High energy but moisture sensitive |
| Prussian blue analogues | Na₂MnFe(CN)₆ | 3.0–3.5 V | 120–160 | Easy synthesis; moisture sensitivity |
| NASICON-type phosphates | Na₃V₂(PO₄)₃ | 3.4 V | 117 | Stable but contains V (cost/toxicity) |

## Anode Materials

Graphite does not intercalate Na⁺ efficiently (interlayer spacing too small). **Hard carbon** (disordered, non-graphitisable carbon from biomass or resin pyrolysis) is the preferred anode:
- Capacity: 250–350 mAh/g
- First-cycle Coulombic efficiency: 80–88% (sodium SEI consumes ~15% of capacity)
- Rate capability: Good for moderate C-rates

**Alloy anodes** (Sn, Sb): Higher capacity but severe volume expansion (~300%) limits cycle life — under active development.

## Performance vs Lithium-Ion

| Property | SIB | LFP (LIB) | NMC 811 (LIB) |
|----------|-----|-----------|----------------|
| Gravimetric energy density | 100–160 Wh/kg | 90–120 Wh/kg | 200–280 Wh/kg |
| Volumetric energy density | 200–300 Wh/L | 200–350 Wh/L | 400–700 Wh/L |
| Cycle life | 1000–3000 | 2000–4000 | 500–1500 |
| Low-temperature performance | Better than LFP | Good | Moderate |
| Cost potential | Lower | Low | Higher |

## Applications

SIBs are not trying to displace NMC for electric vehicles — they are positioned for:
- **Stationary grid storage** (less weight-sensitive): Renewables firming, peak shaving.
- **Low-cost EVs and e-bikes**: BYD Seagull (LFP/SIB mix pack, 2023) proved commercial viability.
- **Industrial equipment**: Forklifts, AGVs where energy density is secondary to cycle life and cost.

## Commercialisation

- **CATL**: Released first-generation SIB pack in 2023; targets 160 Wh/kg by 2025.
- **HiNa Battery (CNRS spin-off)**: Cathode: Na-Fe-Mn oxide; commercialising in China.
- **Faradion (UK, acquired by Reliance Industries)**: O3-type layered oxide cathode.
- **Natron Energy (US)**: Prussian blue anode and cathode; targets data centre UPS with >50,000 cycle claims.
MD,
            ],

            [
                'title'   => 'Supercapacitors: Electrochemical Double-Layer Capacitors and Pseudocapacitors',
                'summary' => 'How supercapacitors store energy through electrostatic and Faradaic mechanisms, where they outperform batteries, and their role in hybrid energy storage systems.',
                'tags'    => ['supercapacitor', 'EDLC', 'pseudocapacitor', 'energy storage', 'power density', 'activated carbon'],
                'content' => <<<MD
# Supercapacitors: Electrochemical Double-Layer Capacitors and Pseudocapacitors

Supercapacitors (also called ultracapacitors) bridge the gap between conventional capacitors (high power, low energy) and batteries (low power, high energy). They store energy electrochemically but without bulk Faradaic reactions, enabling very high power density and cycle lives exceeding one million cycles.

## Ragone Chart Position

| Device | Energy Density (Wh/kg) | Power Density (W/kg) | Cycle Life |
|--------|----------------------|---------------------|------------|
| Li-ion battery | 100–250 | 500–2000 | 500–3000 |
| Supercapacitor (EDLC) | 3–10 | 5,000–20,000 | >500,000 |
| Pseudocapacitor | 15–50 | 2,000–10,000 | 10,000–100,000 |
| Lead-acid battery | 25–40 | 75–300 | 200–500 |

## Electrochemical Double-Layer Capacitor (EDLC)

**Mechanism**: Electrostatic adsorption of electrolyte ions on high-surface-area electrode surfaces forms a Helmholtz double layer. No charge transfer across the electrode–electrolyte interface.

**Energy storage**: E = ½CV² (where C is capacitance)

**Capacitance**: C ∝ ε_r × A / d (permittivity × electrode area / double-layer thickness ~0.5 nm)

**Electrode material**: Activated carbon with surface area 1,000–3,000 m²/g is the commercial standard. Higher surface area does not always mean higher capacitance — pore size must match electrolyte ion size (optimum ~1–2 nm micropores).

**Electrolytes**:
- Aqueous (H₂SO₄, KOH, Na₂SO₄): Low ESR, but voltage window limited to ~1 V.
- Organic (TEABF₄ in acetonitrile or propylene carbonate): 2.5–2.7 V window; standard for commercial cells.
- Ionic liquids: 3.5–4 V window; low conductivity at room temperature.

**Capacitance ≈ 100–300 F/g** for activated carbon in organic electrolyte.

## Pseudocapacitors

Store energy through fast, reversible surface Faradaic reactions — not intercalation, which means they retain fast kinetics.

**Materials**:
- **Ruthenium dioxide (RuO₂)**: 720 F/g (hydrous form); proton insertion: RuOₓ(OH)y + δH⁺ + δe⁻ ↔ RuOₓ₋δ(OH)y₊δ. Too expensive for mass market.
- **Manganese dioxide (MnO₂)**: ~300–400 F/g; low cost; limited to thin films due to poor conductivity.
- **Conducting polymers** (PANI, PPy): Redox active; high specific capacitance but poor cycle life due to swelling.

## Hybrid Supercapacitors

Combine EDLC electrode (fast, high power) with battery-type electrode (slow, high energy):
- **Lithium-ion capacitor (LIC)**: Pre-lithiated graphite anode (battery-type) + activated carbon cathode (EDLC).
  - Energy density: 15–30 Wh/kg (3–5× EDLC); power density retained.
  - Challenge: pre-lithiation adds complexity and cost.
- **Sodium-ion capacitor**: Hard carbon anode + activated carbon cathode.

## Applications

1. **Regenerative braking**: Hybrid buses, trams — capture braking energy pulses (high power, short duration).
2. **Engine start-stop**: Cranking power without high charge/discharge rate stress on Li-ion.
3. **Grid power quality**: Sub-second frequency response; faster than any battery.
4. **UPS bridging**: Ride-through while diesel generators start (30–60 s).
5. **Industrial cranes, elevators**: Peak-shave motor start current.

## Key Limitation

Energy density (~5–10 Wh/kg) is 10–30× lower than lithium-ion. Self-discharge rate is higher than batteries (~2–40% per day depending on type). Not suited for long-duration storage.
MD,
            ],

            [
                'title'   => 'Grid-Scale Energy Storage: Technologies, Economics, and Use Cases',
                'summary' => 'An overview of the main technologies used for grid-scale energy storage — from pumped hydro and BESS to compressed air and thermal storage — with economics and suitability for different grid services.',
                'tags'    => ['grid storage', 'BESS', 'pumped hydro', 'energy storage', 'renewables', 'grid services'],
                'content' => <<<MD
# Grid-Scale Energy Storage: Technologies, Economics, and Use Cases

Grid-scale energy storage is increasingly essential for integrating variable renewable generation (wind, solar) into electricity systems that must match supply to demand in real time.

## Grid Services Taxonomy

| Service | Response Time | Duration | Value Driver |
|---------|-------------|----------|-------------|
| Primary frequency response | <30 s | Seconds | Grid stability; high value per MW |
| Secondary/tertiary frequency | 30 s–30 min | Minutes | Frequency restoration |
| Arbitrage | Minutes–hours | Hours | Buy cheap, sell expensive |
| Capacity adequacy | Hours | 4–12 h | Firm capacity during peak demand |
| Long-duration storage | Hours–days | >8 h | Seasonal balancing, renewable firming |

## Technology Overview

### Pumped Hydroelectric Storage (PHS)
- **Installed capacity**: ~95% of global grid storage (160 GW+).
- **Principle**: Pump water uphill during off-peak; release through turbines at peak.
- **Round-trip efficiency**: 70–85%.
- **LCOE**: $50–150/MWh (very site-dependent).
- **Duration**: 6–24 hours.
- **Limitation**: Geography; long development time (10–15 years); environmental permitting.

### Battery Energy Storage Systems (BESS)

The fastest-growing segment. Dominated by lithium-ion (LFP chemistry for long-cycle stationary applications).

**Key metrics**:
- Round-trip efficiency: 85–92% (AC-AC, including inverter losses).
- Capital cost: $200–350/kWh (cell + BOS + installation, 2024 prices).
- Cycle life: LFP ≥3000 cycles at 80% DoD.
- Response time: <1 second (limited by inverter control loop).

**Use cases well-suited for BESS**: Frequency regulation, arbitrage (2–4 h), solar peak shifting, capacity firming.

**BESS project structure**:
- Battery modules → racks → containers (1–4 MWh per container).
- Power conversion system (PCS): Bidirectional inverter + transformer.
- Energy Management System (EMS): Dispatch optimisation, SoC management.
- Fire suppression: NFPA 855 compliant; aerosol or HFC systems.

### Compressed Air Energy Storage (CAES)

- **Principle**: Compress air into underground caverns (salt caverns, depleted gas fields) during off-peak; expand through turbines at peak.
- **Adiabatic CAES**: Stores heat of compression separately → round-trip efficiency 60–70%.
- **Isothermal CAES**: Near-isothermal compression → theoretical efficiency >80% (still pre-commercial at grid scale).
- **Status**: Only two commercial plants worldwide (Huntorf, Germany 1978; McIntosh, Alabama 1991).
- **Limitation**: Very site-specific (geological requirements); geographic constraint similar to PHS.

### Flow Batteries

Electrolyte stored in external tanks; capacity and power are decoupled.
- **Vanadium redox flow battery (VRFB)**: Most mature; 20+ year electrolyte life (electrolyte retains value); round-trip efficiency 65–75%.
- **Iron-air battery**: Iron/iron oxide electrolyte; very low cost; but round-trip efficiency ~45–50%.
- **Suitability**: 4–12 hour duration; excellent for long-duration grid applications.

### Thermal Energy Storage (TES)

- **Molten salt** (concentrated solar power): 560–600 °C molten salt stores heat for 4–16 hours. Provides firm dispatchable solar power at night.
- **Ice storage**: Freeze water at night (cheap electricity) → melt for building cooling during peak. Proven technology in commercial buildings.

## LCOE and Duration Sweet Spots

| Technology | Capital Cost ($/kWh) | Best Duration | Round-Trip Efficiency |
|-----------|---------------------|--------------|----------------------|
| PHS | $100–350 | 6–24 h | 70–85% |
| Li-ion BESS | $200–350 | 1–4 h | 85–92% |
| VRFB | $300–500 | 4–12 h | 65–75% |
| CAES | $50–100 | 8–24 h | 60–70% |
| Molten salt TES | $15–30/kWh_th | 8–16 h | 93% (thermal only) |

The transition from $/kWh to $/kW (power capacity cost) and the **number of cycles per year** both dramatically affect levelised cost and technology selection.
MD,
            ],

            [
                'title'   => 'Battery Management Systems: SoC Estimation, Cell Balancing, and Thermal Management',
                'summary' => 'How a BMS monitors and controls a battery pack to maximise performance, safety, and lifespan — covering state estimation algorithms, passive/active balancing, and thermal design.',
                'tags'    => ['BMS', 'battery management', 'SoC estimation', 'cell balancing', 'thermal management', 'energy storage'],
                'content' => <<<MD
# Battery Management Systems: SoC Estimation, Cell Balancing, and Thermal Management

A Battery Management System (BMS) is the embedded electronic system that monitors and controls a battery pack. It protects the cells from operating outside their safe operating area (SOA), extends cycle life, and provides accurate state information to the host system.

## Core BMS Functions

1. **Cell voltage monitoring**: Individual cell voltages sampled at 1–100 Hz. Typical resolution: ±1 mV.
2. **Temperature monitoring**: Thermistors at multiple pack locations; cell surface and coolant inlet/outlet.
3. **Current measurement**: Hall-effect sensor or shunt resistor (±0.1–0.5% accuracy).
4. **State estimation**: SoC, SoH, SoP.
5. **Fault detection and protection**: Over/undervoltage, over/undertemperature, over-current, short circuit.
6. **Cell balancing**: Equalise charge across cells.
7. **Communication**: CAN bus (automotive), Modbus/CANopen (stationary), SMBus (consumer electronics).

## State-of-Charge (SoC) Estimation

SoC is the fraction of usable energy remaining relative to full charge. It cannot be measured directly.

### Coulomb Counting
SoC(t) = SoC(0) − (1/Q_rated) ∫₀ᵗ I(τ) dτ

- Simple, low computational cost.
- Error accumulates from current measurement noise; requires periodic recalibration.
- Accurate only if initial SoC is known.

### Open Circuit Voltage (OCV) Method
Map rested cell voltage → SoC from a characterisation curve.
- Accurate at equilibrium — requires ≥1 hour rest time.
- LFP has a flat OCV-SoC plateau (3.2–3.3 V for 10–90% SoC) — very poor resolution.

### Extended Kalman Filter (EKF)
State-space model of cell electrochemical dynamics; Kalman filter fuses OCV estimate and Coulomb counting.
- Handles sensor noise and model uncertainty.
- Requires accurate equivalent circuit model parameters (R₀, R₁, C₁ — identified via electrochemical impedance spectroscopy).
- Accuracy: ±2–5% SoC under dynamic conditions.

### Data-Driven Methods
LSTM, neural network SoC estimators trained on charge/discharge profiles.
- High accuracy (±1–2%) after training on representative data.
- Requires large labelled dataset; model generalisation across temperatures/aging states is a challenge.

## Cell Balancing

Manufacturing tolerance causes cells in a series string to have slightly different capacities. Over cycling, imbalance grows: the weakest cell limits pack capacity and is over-stressed.

### Passive Balancing
Dissipate excess energy from higher-SoC cells as heat through a resistor.
- Simple, low cost.
- Energy wasted (not transferred to low-SoC cells).
- Heat generation limits balancing current (typically 100–500 mA).

### Active Balancing
Transfer energy from higher-SoC to lower-SoC cells via DC-DC converters (inductors, transformers, capacitors).
- **Cell-to-cell**: Switched capacitor or inductive shuttle.
- **Cell-to-bus-to-cell**: Central DC-DC converter with shared bus.
- Higher efficiency (typically 80–95%) but more complex and costly.
- Essential for large packs (>100 cells in series) and applications with high degradation rates.

## Thermal Management

Cell performance and degradation are strongly temperature-dependent:
- **Optimal operating range**: 15–35 °C (charging); 0–45 °C (discharging).
- **Below 0 °C**: Li plating risk during charging → avoid charging below 5 °C unless rate-limited.
- **Above 45 °C**: Accelerated SEI growth and capacity fade.

### Thermal Management Approaches

| Method | Coolant | Thermal Resistance | Complexity | Use Case |
|--------|---------|-------------------|-----------|---------|
| Air cooling | Air | High | Low | Consumer electronics, low-C-rate packs |
| Indirect liquid | Glycol-water | Medium | Moderate | EV packs (BMW i3, Model 3 base) |
| Direct liquid | Dielectric fluid | Low | High | High-performance EVs, data centre UPS |
| Phase change material (PCM) | Paraffin, salt hydrate | Low | Moderate | Passive; absorbs heat of fusion |
| Immersion cooling | Dielectric oil / engineered fluid | Very low | High | Extreme fast charge (>6C) |

**Temperature uniformity** within a pack is as important as absolute temperature — cell-to-cell ΔT >5 °C accelerates imbalance. Thermal management design must ensure ΔT <3–5 °C across all cells.
MD,
            ],

            [
                'title'   => 'Second-Life Batteries and End-of-Life Recycling',
                'summary' => 'How retired EV battery packs are evaluated for second-life stationary storage applications, and the current state of lithium-ion battery recycling — hydrometallurgy, pyrometallurgy, and direct recycling.',
                'tags'    => ['second-life battery', 'battery recycling', 'hydrometallurgy', 'pyrometallurgy', 'circular economy', 'energy storage'],
                'content' => <<<MD
# Second-Life Batteries and End-of-Life Recycling

The rapid scale-up of electric vehicles will produce millions of battery packs reaching end-of-vehicle-life (~2028–2035 peak). These packs typically retain 70–80% of original capacity — enough for stationary storage applications before eventual recycling. Managing this material stream is critical for supply chain sustainability and circular economy goals.

## Second-Life Battery Economics

### Why Second Life?

A pack retired from a vehicle at 80% SoH (state of health) still has significant electrochemical capacity. For stationary storage, which is less weight- and volume-constrained than a vehicle, this degraded pack can provide value for an additional 5–10 years.

**Economic drivers**:
- Retired pack cost: Historically $30–80/kWh (fraction of new cell cost).
- Revenue from additional storage service: $50–150/MWh over second-life period.
- Avoids immediate recycling cost (net positive in some jurisdictions).

### Technical Challenges

1. **State of Health assessment**: Each pack must be individually characterised — capacity test (C/5 discharge), impedance spectroscopy, or data-driven degradation model. This is costly and time-consuming at scale.
2. **Heterogeneous inventory**: Different chemistries, form factors, BMS protocols from multiple OEMs.
3. **Liability**: Who is responsible for a second-life pack failure — the OEM, the refurbisher, or the new operator?
4. **Safety**: Degraded packs have increased lithium plating risk; thermal management design for second life must account for cell variability.

### Second-Life Applications

| Application | Required SoH | Duration | Second-Life Fit |
|-------------|-------------|----------|----------------|
| Solar self-consumption | >70% | 2–4 h | Good |
| Frequency regulation | >75% | Seconds–minutes | Good (power, not energy) |
| Peak shaving | >70% | 1–4 h | Good |
| Long-duration grid | >85% | 8–12 h | Challenging |

## End-of-Life Recycling

When packs can no longer serve any economic purpose, valuable materials must be recovered. Li-ion cells contain cobalt (up to 15% in NCA/NMC), lithium, nickel, manganese, copper, and aluminium.

### Discharge and Dismantling
Cells must be discharged to <1 V before shredding to prevent fires. Manual dismantling is labour-intensive; automated dismantling lines are under development (e.g., Volkswagen's Salzgitter facility, Li-Cycle).

### Pyrometallurgy (Smelting)

- Feed shredded cells directly into a high-temperature furnace (1200–1500 °C).
- Recovers Co, Ni, Cu in a metal alloy; Li, Mn, Al lost in slag.
- Simple, handles mixed chemistries without sorting.
- Low recovery of Li and Mn; high energy consumption; slag requires downstream treatment.
- **Companies**: Umicore, Glencore.

### Hydrometallurgy

- Mechanical pre-processing → "black mass" (cathode + anode materials after separating metals).
- Leach black mass in acid (H₂SO₄ + H₂O₂) → solution of Li, Co, Ni, Mn.
- Selective precipitation or solvent extraction → individual metal salts or precursors.
- **Recovery rates**: Co >95%, Ni >95%, Li 70–90%, Mn >90%.
- **Companies**: Li-Cycle, Retriev Technologies, Ganfeng Lithium.

### Direct Recycling

Preserve cathode crystal structure — regenerate rather than dissolve and re-precipitate.
- Shred → separate black mass → hydrothermally relithiate degraded cathode material.
- Re-synthesises near-virgin cathode without full dissolution.
- Lower energy input; maintains cathode material value.
- Challenge: Works best with single chemistry, controlled separation.
- **Status**: Pre-commercial (ReCell Center, Ascend Elements).

## EU Battery Regulation 2023

Establishes mandatory requirements for batteries placed on the EU market:
- **Carbon footprint declaration**: Mandatory from 2025 (LIBs >2 kWh).
- **Recycled content targets**: By 2031 — 6% Li, 6% Co, 6% Ni in new batteries from recycled sources.
- **Collection rate targets**: 73% by 2030.
- **Battery passport**: Digital record of chemistry, SoH, recycled content from 2026.

China's equivalent regulation (GB/T 34015 series) similarly mandates traceability and recovery targets.
MD,
            ],
        ];

        foreach ($entries as $entry) {
            BasicKnowledgeTrend::firstOrCreate(
                ['title' => $entry['title']],
                array_merge($entry, ['category_id' => $category->id, 'status' => 'published'])
            );
        }
    }
}
