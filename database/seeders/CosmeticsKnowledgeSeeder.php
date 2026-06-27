<?php

namespace Database\Seeders;

use App\Models\BasicKnowledgeTrend;
use App\Models\Category;
use Illuminate\Database\Seeder;

class CosmeticsKnowledgeSeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::firstOrCreate(
            ['name' => 'Cosmetics & Personal Care'],
            ['slug' => 'cosmetics-personal-care']
        );

        $entries = [
            [
                'title'   => 'Emulsion Technology: O/W and W/O Systems in Cosmetic Formulation',
                'summary' => 'How oil-in-water and water-in-oil emulsions differ structurally, how emulsifiers stabilise them, and when to choose each system for skin and hair products.',
                'tags'    => ['emulsion', 'cosmetic formulation', 'emulsifier', 'HLB', 'skin care'],
                'content' => <<<MD
# Emulsion Technology: O/W and W/O Systems in Cosmetic Formulation

Emulsions are thermodynamically unstable dispersions of two immiscible phases — water and oil — stabilised by surfactants or polymeric emulsifiers. The vast majority of lotions, creams, and conditioners are emulsions.

## Phase Types

| Type | Continuous Phase | Dispersed Phase | Skin Feel |
|------|-----------------|-----------------|-----------|
| O/W (oil-in-water) | Water | Oil droplets | Light, non-greasy, fast absorbing |
| W/O (water-in-oil) | Oil | Water droplets | Rich, occlusive, protective |
| W/Si (water-in-silicone) | Silicone | Water droplets | Silky, long-lasting, water-resistant |

## The HLB System

Hydrophilic–Lipophilic Balance (HLB) scores from 1–20 guide emulsifier selection:
- **HLB 3–6**: W/O emulsifiers (e.g., sorbitan monooleate, Span 80)
- **HLB 8–16**: O/W emulsifiers (e.g., polysorbate 20, cetearyl glucoside)
- **Blending rule**: Mix two emulsifiers to hit a target HLB — this also improves interfacial film stability.

## Stability Mechanisms

1. **Electrostatic repulsion** — anionic emulsifiers (e.g., sodium stearoyl glutamate) create a negatively charged droplet surface that repels neighbouring droplets.
2. **Steric stabilisation** — polymeric emulsifiers (e.g., PEG-modified silicones, Carbomer) form a physical barrier around droplets.
3. **Lamellar liquid crystal network** — fatty alcohol + emulsifier combinations (cetearyl alcohol + ceteareth-20) create ordered bilayers that immobilise the dispersed phase.

## Processing Considerations

- **Hot–hot process**: Both phases heated to 70–80 °C, water phase added to oil phase under homogenisation. Standard for most creams.
- **Cold process**: Emulsification at ambient temperature using pre-emulsified concentrates. Energy-saving but limits active loading.
- **Phase inversion temperature (PIT)**: Transitioning through the inversion temperature creates very fine droplets; used for nanoemulsions.

## Formulation Stability Testing

Accelerated stability protocols: 40 °C/75% RH for 3 months; freeze-thaw cycling (–10 °C to +40 °C, 3 cycles); centrifuge at 3000 rpm for 30 minutes. Pass criteria: no phase separation, no significant viscosity drift, no organoleptic change.
MD,
            ],

            [
                'title'   => 'Preservative Systems in Cosmetics: Efficacy, Safety, and Regulatory Landscape',
                'summary' => 'A practical guide to choosing and challenging preservative systems — from parabens and phenoxyethanol to multi-functional actives and natural alternatives.',
                'tags'    => ['preservatives', 'cosmetic safety', 'parabens', 'phenoxyethanol', 'antimicrobial'],
                'content' => <<<MD
# Preservative Systems in Cosmetics: Efficacy, Safety, and Regulatory Landscape

Microbial contamination of cosmetics causes product spoilage and, in rare cases, consumer infection. Preservatives are added to inhibit bacterial, yeast, and mould growth throughout a product's shelf life and in-use period.

## Primary Preservative Classes

| Class | Examples | Spectrum | Notes |
|-------|----------|----------|-------|
| Parabens | Methylparaben, propylparaben | Broad | Effective, inexpensive; EU restricts propyl/butyl to 0.14% in leave-on |
| Organic acids | Benzoic acid, sorbic acid | Fungi/gram-positive bacteria | pH-dependent; active form is undissociated acid |
| Phenol derivatives | Phenoxyethanol | Broad | Widely used; concerns about dermal sensitisation at high levels |
| Formaldehyde releasers | DMDM hydantoin, imidazolidinyl urea | Broad | Declining use; formaldehyde sensitisation risk |
| Multi-functional | Caprylyl glycol, ethylhexylglycerin | Broad (booster) | Enhance primary preservative efficacy |

## Preservation Efficacy Testing (PET)

ISO 11930 / USP 51 challenge tests:
- Inoculate with *Staphylococcus aureus*, *Pseudomonas aeruginosa*, *E. coli*, *Candida albicans*, *Aspergillus brasiliensis*
- Count colony-forming units at days 2, 7, 14, 28
- Criteria A (leave-on): ≥2 log reduction in bacteria by day 2; no increase in fungi by day 14

## Formulation Variables Affecting Preservation

- **pH**: Most preservatives are far more active at pH ≤5.5. A formula at pH 6.5 may need 2–3× the load of the same formula at pH 4.5.
- **Water activity (Aw)**: Gels with high humectant content (>30% glycerin) can achieve Aw <0.85, making microbial growth difficult even without conventional preservatives.
- **Oil phase volume**: High oil content reduces the aqueous phase concentration of water-soluble preservatives — correct for true aqueous concentration.

## Natural and Preservative-Free Strategies

- **Anhydrous formats** (balms, oils, powders): No water activity, no preservation needed.
- **High-humectant systems**: Aw depression with propylene glycol, glycerin, or pentylene glycol.
- **Fermentation-derived actives**: Lactobacillus ferment filtrates provide intrinsic antimicrobial activity but require validation.
- **pH-shift**: Some formulas designed at pH 3.5–4.0 are self-preserving for rinse-off products.

## Regulatory References

- EU Cosmetics Regulation 1223/2009, Annex V (permitted preservatives and limits)
- ISO 11930:2019 (efficacy evaluation)
- US FDA 21 CFR 700–740 (no positive list; safety must be substantiated)
MD,
            ],

            [
                'title'   => 'UV Filters and SPF: Chemistry, Testing, and Formulation Challenges',
                'summary' => 'How organic and inorganic UV filters work, how SPF is measured, and the formulation challenges of building stable, cosmetically elegant sun protection products.',
                'tags'    => ['SPF', 'UV filter', 'sunscreen', 'UVA', 'UVB', 'photostability'],
                'content' => <<<MD
# UV Filters and SPF: Chemistry, Testing, and Formulation Challenges

Sunscreen products protect skin by absorbing or reflecting ultraviolet radiation. The two categories — organic (chemical) filters and inorganic (physical) filters — operate through fundamentally different mechanisms.

## UV Spectrum

| Region | Wavelength | Skin Effect |
|--------|-----------|-------------|
| UVB | 290–320 nm | Sunburn, DNA damage, skin cancer |
| UVA II | 320–340 nm | Tanning, photoageing |
| UVA I | 340–400 nm | Deep dermal penetration, photoageing |

## Organic UV Filters

Absorb UV photons and release energy as heat or fluorescence. Key examples:
- **Avobenzone (Butyl methoxydibenzoylmethane)**: Broadest UVA coverage (360 nm peak) but photolabile — pairs with photostabilisers (Tinosorb S, octocrylene).
- **Octocrylene**: UVB absorber and photostabiliser for avobenzone.
- **Tinosorb M / S (bis-ethylhexyloxyphenol methoxyphenyl triazine)**: Broad-spectrum, photostable; EU-approved, not yet approved in US.
- **Mexoryl SX / XL**: UVA filters in L'Oréal patents; approved EU/Canada, not US FDA.

## Inorganic UV Filters

Scatter and reflect UV (and some visible) radiation.
- **Zinc oxide (ZnO)**: Excellent broad-spectrum UVA coverage; forms stable dispersions.
- **Titanium dioxide (TiO₂)**: Strong UVB; less UVA-I than ZnO. Surface-coated nanoparticles reduce whitening.

## SPF Measurement

ISO 24444 (in vivo, human subjects):
- Apply 2 mg/cm² on volunteer backs
- Determine MED (minimum erythemal dose) for protected vs unprotected skin
- SPF = MED(protected) / MED(unprotected)

In vitro SPF (ISO 24443) is used for development but not label claims.

## Formulation Challenges

1. **Whitening**: Inorganic filters scatter visible light. Nano-sized particles (<100 nm) reduce whitening but raise safety debate.
2. **Photostability**: Avobenzone degrades 50–90% within 1 hour of sun exposure without a photostabiliser.
3. **Skin feel**: High concentrations of organic filters feel greasy; silicone carriers (cyclopentasiloxane) improve elegance.
4. **Filter compatibility**: Some combinations cause eutectic melting (liquid at room temperature) — e.g., oxybenzone + avobenzone — reducing stability.
5. **Water resistance**: Tested per ISO 16217; film-forming polymers (acrylates copolymer) or wax matrices retain SPF after water immersion.

## Regulatory Differences

- **EU**: Positive list (Annex VI of Reg 1223/2009); ~28 permitted filters.
- **US FDA**: Only 16 filters permitted; Tinosorb and Mexoryl pending FDA approval since >2003.
- **Australia TGA**: Similar to EU; broad-spectrum mandatory for SPF50+.
MD,
            ],

            [
                'title'   => 'Surfactants in Hair Care: Cleansing, Conditioning, and Mildness',
                'summary' => 'How anionic, amphoteric, and non-ionic surfactants work in shampoos and conditioners, and the trade-offs between cleansing power and scalp/hair compatibility.',
                'tags'    => ['surfactant', 'shampoo', 'hair care', 'anionic surfactant', 'conditioner', 'mildness'],
                'content' => <<<MD
# Surfactants in Hair Care: Cleansing, Conditioning, and Mildness

Surfactants are the functional core of every shampoo. They must solubilise sebum and product build-up efficiently while remaining compatible with scalp skin and colour-treated or chemically processed hair.

## Surfactant Classes in Shampoo

| Class | Example | CMC (approx.) | Foaming | Mildness |
|-------|---------|---------------|---------|----------|
| Anionic | SLES (sodium laureth sulfate) | 0.2 mM | High | Moderate |
| Anionic | SCS (sodium cocoyl sulfate) | 1 mM | High | Low |
| Anionic | SCI (sodium cocoyl isethionate) | — | Moderate | High |
| Amphoteric | Cocamidopropyl betaine (CAPB) | 1–3 mM | Moderate | Very high |
| Non-ionic | Decyl glucoside | — | Low | Very high |
| Anionic | Sodium lauroyl methyl isethionate | — | Moderate | Very high |

## SLES vs SLS

Sodium lauryl sulfate (SLS, C12) is more irritating than sodium laureth sulfate (SLES, ethoxylated). SLES with ≥3 EO units has substantially lower transepidermal water loss (TEWL) induction. Most modern shampoos use SLES as primary + CAPB as co-surfactant (synergy: lower irritation, improved foam quality).

## Conditioning Agents in Shampoo (2-in-1)

- **Cationic polymers** (polyquaternium-10, guar hydroxypropyltrimonium chloride): Deposit on negatively charged hair during rinsing; provide detangling and shine.
- **Silicone emulsions** (dimethicone, amodimethicone): Deposit on damaged cuticle; fill surface defects; reduce friction.
- **Deposition mechanism**: Cationic charge attracted to anionic hair surface as product is diluted with water during rinsing; surfactant micelles break apart releasing conditioning agent.

## Rinse-off Conditioners

Classic conditioner formula relies on a cationic lamellar liquid crystal (LLC) system:
- **Cetrimonium chloride** or **behentrimonium chloride** (BTAC): Primary cationic conditioning agent
- **Fatty alcohols** (cetyl, cetearyl): Thickener and LLC former
- **Dimethicone emulsion**: Smoothing and shine
- Cationic charge prevents rinsing off until mechanical effort applied

## pH Considerations

Hair has isoelectric point around pH 3.67. Shampoos are typically formulated pH 5–6 to match scalp skin. Alkaline pH swells the cuticle, increasing porosity and static charge. Acid rinses (citric acid) flatten cuticle temporarily — important for colour-treated hair.
MD,
            ],

            [
                'title'   => 'Active Ingredients in Skincare: Mechanism, Concentration, and Stability',
                'summary' => 'Evidence-based overview of key skincare actives — retinoids, vitamin C, niacinamide, AHA/BHA — including how they work, effective concentrations, and formulation pitfalls.',
                'tags'    => ['actives', 'retinol', 'vitamin C', 'niacinamide', 'AHA', 'skin care formulation'],
                'content' => <<<MD
# Active Ingredients in Skincare: Mechanism, Concentration, and Stability

"Active" ingredients have demonstrated clinical efficacy at specific concentrations. This entry covers the most commercially significant molecules, their mechanism of action, and formulation constraints.

## Retinoids

| Form | Conversion Steps to Retinoic Acid | Efficacy | Irritation |
|------|----------------------------------|---------|------------|
| Tretinoin (retinoic acid) | None | Highest | High |
| Retinal (retinaldehyde) | 1 step | High | Moderate |
| Retinol | 2 steps | Moderate | Low–moderate |
| Retinyl esters | 3 steps | Low | Low |

**Mechanism**: Bind retinoic acid receptors (RAR) → increase epidermal turnover, stimulate collagen I and III synthesis, inhibit MMP-1 (collagenase).

**Formulation**: Retinol oxidises rapidly in light and air. Stabilise with vitamin E tocopherol, encapsulation (lipid nanoparticles), or use in anhydrous systems. Typical effective OTC range: 0.025–1% retinol.

## Vitamin C (L-Ascorbic Acid)

**Mechanism**: Antioxidant (neutralises reactive oxygen species); cofactor for prolyl hydroxylase → collagen synthesis; melanin synthesis inhibitor via tyrosinase inhibition.

**Effective concentration**: ≥10% L-ascorbic acid; peak efficacy around 20%.

**pH dependency**: LAA must be formulated at pH ≤3.5 to penetrate stratum corneum. This limits compatibility with many other actives and causes stinging on sensitive skin.

**Stability solutions**: Derivatives with greater stability (ascorbyl glucoside, ethyl ascorbic acid, ascorbyl tetraisopalmitate) sacrifice some bioavailability for shelf life.

## Niacinamide (Nicotinamide)

**Mechanism**: Inhibits transfer of melanosomes from melanocytes to keratinocytes (reduces hyperpigmentation); increases ceramide synthesis (barrier repair); anti-inflammatory via NF-κB pathway suppression.

**Concentration**: 2–5% for pigmentation; up to 10% for sebum regulation/pore appearance.

**Stability**: Highly water-soluble and stable across pH 4–7. At elevated temperatures, niacinamide can convert to nicotinic acid (niacin), causing flushing — avoid prolonged high-temperature processing.

**Niacinamide + Vitamin C myth**: The "niacin flush" from this combination is largely a myth at cosmetic concentrations and ambient storage temperatures. Clinical studies show co-formulation is safe and effective.

## Alpha Hydroxy Acids (AHA)

- **Glycolic acid** (C2): Smallest molecule, best penetration; used for exfoliation and hyperpigmentation.
- **Lactic acid** (C3): Gentler, also humectant; used in dry/sensitive skin formulas.
- **Mandelic acid** (C8): Largest, slowest penetration; suited for reactive skin and acne.

**Mechanism**: Reduce corneocyte cohesion in stratum corneum → accelerate desquamation; stimulate dermis via receptor pathways at higher concentrations.

**Effective concentration**: 5–10% at pH 3–4 for exfoliation. EU limits leave-on AHA to 10% and requires sun protection warning.

## Salicylic Acid (BHA)

**Mechanism**: Lipophilic — penetrates sebaceous follicles; keratolytic; anti-inflammatory via salicylate pathway.

**Effective concentration**: 0.5–2% (EU OTC limit 2% leave-on).

**Formulation**: Active only as undissociated acid — requires pH ≤3.5 for maximum activity; at pH 4.5, only ~10% is in active form.
MD,
            ],

            [
                'title'   => 'Colour Cosmetics: Pigments, Binders, and Film-Forming Systems',
                'summary' => 'How inorganic and organic pigments are selected and dispersed in foundations, lipsticks, and eye products, and the role of film formers and waxes in performance.',
                'tags'    => ['pigment', 'colour cosmetics', 'foundation', 'lipstick', 'film former', 'titanium dioxide'],
                'content' => <<<MD
# Colour Cosmetics: Pigments, Binders, and Film-Forming Systems

Colour cosmetics require precise control of pigment dispersion, skin adhesion, wear resistance, and transfer resistance. The category includes foundations, concealers, lipsticks, eyeshadows, and mascaras.

## Pigment Types

### Inorganic Pigments
- **Titanium dioxide (CI 77891)**: White pigment and opacity agent; particle size controls coverage (rutile >200 nm for max opacity).
- **Iron oxides (CI 77491/77492/77499)**: Red, yellow, black oxides blended to match all skin tones; chemically inert; heat-stable.
- **Ultramarines (CI 77007)**: Vivid blues and violets for eye products; not skin-safe in leave-on formulas containing lactic acid.

### Organic Pigments (Lakes)
- Water-insoluble salts of synthetic dyes precipitated onto aluminium hydroxide substrates.
- Higher chroma (saturation) than inorganic pigments.
- FD&C and D&C lakes approved by FDA for cosmetic use; EU uses separate CI numbers.

### Pearlescent/Effect Pigments
- Mica coated with TiO₂ or iron oxide: interference colour from thin-film optics.
- Particle size governs effect: fine (<15 µm) = satin; coarse (>80 µm) = glitter.

## Pigment Dispersion

Pigment agglomerates must be broken down and stabilised in the vehicle. Methods:
- **3-roll mill**: For stiff pastes (lip colour, eye pencil); achieves finest dispersion.
- **High-shear homogeniser**: For fluid products (liquid foundation).
- **Wetting agents**: Castor oil, isononyl isononanoate, or silicone fluids wet pigment surfaces and prevent re-agglomeration.

Surface-treated pigments (e.g., TiO₂ coated with dimethicone or methicone) disperse more easily in silicone-based systems.

## Foundation Systems

| Vehicle Type | Key Ingredients | Finish |
|-------------|-----------------|--------|
| O/W emulsion | SLES, fatty alcohol, glycerin | Natural/dewy |
| W/Si emulsion | Cyclopentasiloxane, dimethicone | Matte/satin, long-wear |
| Anhydrous | Esters, waxes, silicones | Full coverage |
| Powder-to-liquid | Pressed silica + liquid binder | Lightweight |

## Lipstick Structure

A lipstick must have: sufficient hardness for structural integrity, adequate glide for comfortable application, and colour stability for the product lifespan.

- **Waxes** (carnauba, candelilla, beeswax): Structural matrix; increase melting point.
- **Oils** (castor oil, isononyl isononanoate): Glide and shine; too much causes sweating/blooming.
- **Film formers** (polybutene, trimethylsiloxysilicate): Wear resistance and transfer resistance.

Bloom test: product stored at 40 °C for 24 hours then returned to ambient — surface should remain smooth without white film or texture change.

## Mascara

Oil-in-water dispersion with film-forming polymer (e.g., PVP/VA copolymer, acrylates copolymer) that dries to a flexible film on lashes. Waxes thicken the formula and deposit on lashes for volumising. Carbon black or iron oxides provide colour. Ophthalmologist-tested status requires ISO 10993 ocular compatibility testing.
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
