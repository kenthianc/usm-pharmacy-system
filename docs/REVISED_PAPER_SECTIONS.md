# Revised Paper Sections: Evaluation (Pure UAT), Dual-Risk Engine, and Chatbot
**Date Revised:** October 15, 2026  
**Document Purpose:** Review file containing all proposed corrections for the capstone manuscript. Review these sections before copying them into `FINAL_PAPER_OUTLINE.docx`.

---

## PART 1. Evaluation Methodology (User Acceptance Testing Only)

> **Note on Revisions:** All mentions of Likert scales, 1–5 scoring rubrics, and general survey questionnaires have been eliminated. This removes the requirement for external psychometric validators and reliability statistics (e.g., Cronbach's alpha). The research instrument is strictly an **ISTQB-aligned User Acceptance Testing (UAT) Task Sheet** scored via **Pass/Fail** and **Task Acceptance Rate (%)**.

### 1.1 Objective 7 (Evaluation Objective)
> **Objective 7:** To evaluate the functional performance and operational acceptance of the system through User Acceptance Testing (UAT) with intended hospital stakeholders.

*(In the Operational Definitions and Chapter 3, remove "Likert Scale", "Mean Response Score", and any survey definitions).*

---

### 1.2 Project Respondents
The respondents of the evaluation will consist of actual operational stakeholders at the University of Southern Mindanao (USM) Hospital Pharmacy and clinic environment. These include:
1. **Pharmacy Personnel:** Registered Pharmacists ($N = 2$, *confirm exact roster*) who oversee inventory management, prescription verification, and point-of-sale dispensing.
2. **Stock/Inventory Manager:** Dedicated inventory personnel ($N = 1$, or the designated pharmacy personnel handling stock procurement).
3. **Clinic Nurses:** Healthcare personnel ($N = 3 \text{ to } 5$, *confirm exact roster*) who encode and digitally route patient prescriptions from the campus clinic.
4. **IT Expert / Systems Administrator:** Technical personnel ($N = 1$) responsible for campus IT infrastructure, user role access control, and system security.
5. **Patient Users:** University students and resident patients ($N = 10 \text{ to } 15$) who utilize the patient portal to view prescriptions and query the health assistant.

**Sampling Technique:** Purposive sampling is utilized because the application is engineered for the specific operational workflows of the USM Hospital Pharmacy. Meaningful evaluation requires participants with direct domain knowledge of the hospital's clinical and dispensing procedures. Where an individual holds multi-departmental responsibilities, they will execute the task sheet corresponding to each relevant role.

**Ethical Compliance & Data Privacy:** In accordance with the Data Privacy Act of 2012 (Republic Act No. 10173), all patient evaluation sessions will be conducted in a dedicated staging environment populated strictly with anonymized, synthetic (dummy) patient records.

---

### 1.3 Project Instrument: UAT Task Sheet
User Acceptance Testing (UAT) is defined as formal testing conducted by intended users in a representative operational environment to verify whether the system satisfies its specified functional requirements and operational needs (International Software Testing Qualifications Board [ISTQB], 2018).

The **UAT Task Sheet** serves as the sole research instrument for the evaluation. It contains standardized, scenario-based test cases derived directly from the user stories and acceptance criteria defined in the approved System Requirements Specifications (SRS). Scenarios are assigned by role:
* **Clinic Nurse:** Encode a digital prescription, validate drug form/dosage, and route it to the pharmacy dispensary queue.
* **Pharmacist:** Accept a queued prescription, review batch allocations under First-Expired-First-Out (FEFO), apply statutory discounts (Senior/PWD/Student), complete Point-of-Sale (POS) payment, and issue a digital/printed receipt.
* **Stock Manager:** Receive and log a stock delivery, initiate inventory stock adjustments, view near-expiry batches, and review automated stockout/expiry triage analytics.
* **Patient User:** Log into the self-service portal, view active and past prescription records, and consult the Virtual Health Assistant regarding over-the-counter medicine availability and basic first aid.
* **System Administrator:** Provision user credentials, assign role-based permissions, configure hospital operational parameters (lead time, near-expiry alert threshold), and inspect immutable audit logs.

**Scoring Criteria:**  
Each scenario specifies an explicit **Expected Result** taken from the SRS. Each test execution is scored strictly as:
* **Pass:** The observed system output and behavior completely match the stated expected result.
* **Fail:** The system produces an incorrect result, encounters an unhandled exception, or fails to complete the workflow.

For every failed scenario, the specific observed defect is recorded in the *Remarks / Defect Log* column. The evaluation is therefore governed by objective functional acceptance criteria rather than arbitrary perceptual figures.

---

### 1.4 Instrument Validity & Content Review
To guarantee content validity without requiring external psychometric survey validation:
1. **Requirements Coverage:** The researchers will conduct a traceability matrix check to ensure every user story in the SRS corresponds to at least one test scenario, preventing untested functional gaps.
2. **Domain Review:** Prior to deployment, the task sheet will undergo content review by the thesis adviser, the USM Hospital Pharmacist, and an institutional IT representative. They will verify that scenarios reflect actual hospital dispensing procedures, clinical routing protocols, and institutional security controls.
3. **Pre-Test Dry Run:** The researchers will execute all test scenarios once in the staging environment to resolve typographical ambiguities or sequencing issues in the task sheet prior to user administration.

---

### 1.5 Data Collection Procedure
1. **Requirements Gathering:** Pre-development requirements were elicited through formal interviews, workflow observation of pharmacy staff and nurses, and review of existing manual paper logbooks with administrative permission from the USM Hospital.
2. **Evaluation Execution:** Following informed consent and a standardized 15-minute briefing, participants will execute their role-specific task scenarios on a dedicated workstation connected to the staging system.
3. **Observation & Logging:** A designated researcher will observe each interaction without intervening, record whether the scenario resulted in a **Pass** or **Fail**, and transcribe user remarks and encountered defects.
4. **Correction Cycle:** Encountered bugs or functional deficiencies will be cataloged, remediated in code during the evaluation sprint, and re-tested with the respective respondent until all criteria achieve verified resolution.

---

### 1.6 Data Analysis Procedure
Data analysis is performed using the **Task Acceptance Rate ($TAR$)**, defined as the proportion of passed scenarios relative to the total number of executed scenarios, expressed as a percentage:

$$\text{Task Acceptance Rate } (TAR) = \left( \frac{\sum \text{Passed Scenarios}}{\sum \text{Executed Scenarios}} \right) \times 100\%$$

The $TAR$ will be tabulated and reported across two dimensions:
1. **By User Role:** Independent pass rates for Nurse, Pharmacist, Stock Manager, Patient, and Administrator.
2. **System-Wide:** Aggregate pass rate across all modules for both the Initial Test Round (Round 1) and the Verification Round (Round 2, post-bug resolution).

All recorded defects will be summarized in a **Defect Resolution Matrix** detailing:
* Module Name & Scenario ID
* Problem Description & Failure Cause
* Corrective Action Taken in Code
* Retest Verification Status (Pass / Fail)

The system is classified as **Operationally Acceptable** when all functional scenarios achieve a $100\%$ verified Pass status upon conclusion of the correction cycle.

---

## PART 2. Dual-Risk Prediction Model

### 2.1 Theoretical Foundations (RRL Alignment)
The dual-risk inventory engine integrates three established inventory management concepts:
1. **Nasution et al. (2022):** Controlled pharmaceutical stock using Reorder Point (ROP), Safety Stock (SS), and lead-time demand buffers to prevent unexpected stockouts.
2. **Fuentes Acuña (2026):** Utilized historical dispensing frequency to establish moving consumption velocity. Due to the absence of multi-year digitized hospital records required for complex ARIMA/time-series forecasting, a 30-day moving average consumption rate is implemented.
3. **Ahmadi et al. (2022) & Friday and Tiamiyu (2025):** Model pharmaceutical inventory as perishable items subject to strict shelf-life decay, mandating First-Expired-First-Out (FEFO) dispensing order and automated near-expiry alerts.

> **Methodological Note:** The Reorder Point and Safety Stock equations follow standard industrial inventory formulations. The batch-level expiry risk ratio ($ER_b$) is the researchers' novel mathematical formulation designed to model batch-level FEFO depletion velocity against shelf-life constraints.

---

### 2.2 Mathematical Formulations

#### 1. Daily Usage Rate ($DUR$)
The daily consumption velocity for medicine $m$ is computed over a rolling 30-day window:
$$DUR = \frac{\sum_{t=1}^{30} \text{Units Dispensed}_t}{30}$$
* *Boundary Condition:* If a drug has zero dispensing records within the 30-day window, it is flagged as `"No Recent Usage"` for clinical review rather than returning a divide-by-zero error.

#### 2. Stockout Risk Formulation
Let $S$ represent total usable (non-expired) available stock across all active batches, and $L$ represent supplier lead time in days (configurable per facility).

* **Days of Stock Remaining ($DSR$):**
  $$DSR = \frac{S}{DUR}$$
* **Reorder Point ($ROP$) & Safety Stock ($SS$):**
  $$ROP = (DUR \times L) + SS, \qquad SS = Z \times \sigma_d \times \sqrt{L}$$
  * $Z = 1.65$ (95% service level factor).
  * $\sigma_d$ = Standard deviation of daily units dispensed over the 30-day window.
  * $L$ = Supplier lead time in days (Default: 7 days, configurable in Hospital Settings).

* **Stockout Risk Categorization:**
  * **High Risk:** $S \le (DUR \times L)$ *or* $DSR \le L$ (Stock will exhaust before a new order arrives; triggers immediate Purchase Requisition).
  * **Moderate Risk:** $(DUR \times L) < S \le ROP$ (Stock has entered the safety buffer; reorder is due).
  * **Low Risk:** $S > ROP$ (Stock is adequate; normal operations).

#### 3. Expiry Risk Formulation (Batch-Level FEFO Depletion)
For batch $b$ of medicine $m$ possessing remaining quantity $Q_b$:
Let $Q_{prior}$ denote the sum of remaining quantities across all batches of medicine $m$ with an expiration date earlier than batch $b$ ($T_{expiry, b}$), as FEFO protocol mandates these be dispensed first.

* **Projected Depletion Days ($D_b$):**
  $$D_b = \frac{Q_{prior} + Q_b}{DUR}$$
* **Days Remaining Until Expiration ($E_b$):**
  $$E_b = T_{expiry, b} - T_{current}$$
* **Expiry Risk Ratio ($ER_b$):**
  $$ER_b = \frac{D_b}{E_b}$$

* **Expiry Risk Categorization:**
  * **High Risk:** $ER_b > 1.0$ (Projected depletion date exceeds batch expiry; unsold units will expire).
  * **Moderate Risk:** $ER_b \le 1.0$ and $E_b \le \text{Threshold}_{near\text{-}expiry}$ (Batch can be depleted in time, but falls within the hospital's near-expiry warning window, e.g., $\le 60$ days).
  * **Low Risk:** $ER_b \le 1.0$ and $E_b > \text{Threshold}_{near\text{-}expiry}$.

* **Unsold Units at Risk ($Q_{risk}$) & Financial Value at Risk ($VaR$):**
  $$Q_{risk} = \min\Bigl(Q_b,\ \max\bigl(0,\ Q_{prior} + Q_b - (DUR \times E_b)\bigr)\Bigr)$$
  $$VaR = Q_{risk} \times \text{Unit Purchase Cost}$$

---

### 2.3 Recalculation & System Execution
* **Automated Batch Cycle:** An automated console scheduler executes daily at 00:00 (midnight) via `Schedule::command('pharmacy:predict-risk')->daily()`, persisting updated metrics into the database.
* **Manual Recalculation:** Authorized inventory managers can trigger an on-demand recalculation via the administrative dashboard.
* **Verification in Agile Sprint 5:** The engine's calculations will be verified by comparing system triage outputs against manual spreadsheet computations using baseline benchmark datasets.

---

## PART 3. Virtual Health Assistant (Chatbot Scope & Safeguards)

### 3.1 Standardized Scope Statement
> *"The USM Virtual Health Assistant provides general first-aid guidance, basic self-care information, and real-time stock availability checks for over-the-counter (OTC) medicines cataloged in the USM Hospital Pharmacy formulary. The assistant explicitly does not diagnose medical conditions, does not prescribe prescription-only drugs, and does not provide individualized clinical dosing. For any emergency warning signs or symptoms outside the approved minor-symptom list, the system immediately directs users to the USM Health Services clinic or the nearest emergency facility."*

---

### 3.2 Integrated Safeguards
1. **Clinical Red-Flag & Emergency Screening:**  
   Every incoming prompt is pre-screened for critical warning keywords (e.g., *chest pain, difficulty breathing, severe bleeding, loss of consciousness, seizure*). Detection bypasses conversational logic and immediately outputs an emergency warning badge with campus clinic contact numbers.
2. **Formulary-Tied OTC Suggestions:**  
   The assistant cannot invent or hallucinate drug recommendations; OTC suggestions are strictly queried from active inventory marked as OTC-eligible by the pharmacist.
3. **Mandatory Non-Medical Disclaimer:**  
   Every response that references an OTC medicine automatically appends a statutory advisory: *"This information is for general health literacy and does not constitute medical advice or a prescription. Please consult a licensed physician."*
4. **Rate Limiting & Data Privacy:**  
   * **Guest Users:** Limited to 15 queries per session (`MAX_GUEST_MSGS = 15`) with a live remaining-counter to prevent abuse.
   * **Privacy Protection:** In compliance with RA 10173, no personally identifiable information (PII) or student IDs are collected or transmitted in chat interactions.

---

## PART 4. Presentation Slide Blueprint (For Defense)

Use this exact text layout for your defense slide to match department expectations while avoiding validator objections:

```
========================================================================
RESEARCH INSTRUMENT & EVALUATION FRAMEWORK
========================================================================

Main Instrument:
• User Acceptance Testing (UAT) Task Sheet (ISTQB, 2018)

Evaluation Areas (Targeted System Verification):
• Functional Suitability (Prescriptions, Dispensing, POS, Dual-Risk Engine)
• Operational Usability (Role-specific task flows, navigation, clarity)
• System Integrity & Compliance (FEFO enforcement, RBAC, audit logs)

Supporting Evidence & Metrics:
• Task Acceptance Rate (TAR % = Passed Scenarios / Total Scenarios * 100)
• Defect Identification, Remediation, & Retest Log
• Automated System Execution & Database Audit Trails

Content Review & Verification:
• Approved System Requirements Specification (SRS) Acceptance Criteria
• Face & Content Review by USM Hospital Pharmacist & Capstone Adviser
========================================================================
```

---

## Appendix: Sample UAT Task Sheet Matrix (Ready to Print)

| ID | Role | Test Scenario Description | Expected System Result | Observed Result | Pass / Fail | Defect / Remarks |
| :--- | :--- | :--- | :--- | :--- | :---: | :--- |
| **UAT-N1** | Nurse | Encode new patient prescription with 10 tabs Paracetamol 500mg. | Rx generated with unique tracking ID; appears in Pharmacy queue as "Pending". | Verified | **PASS** | Completed without error. |
| **UAT-P1** | Pharmacist | Process queued prescription and dispense using FEFO batching. | System allocates oldest unexpired batch; remaining quantity decrements. | Verified | **PASS** | FEFO deduction verified. |
| **UAT-P2** | Pharmacist | Process POS transaction with 20% Senior Citizen discount. | Total reflects 20% deduction; receipt prints line item with Senior ID. | Verified | **PASS** | Correct discount math. |
| **UAT-S1** | Stock Mgr | Inspect Dual-Risk Dashboard for stock nearing Reorder Point. | Alert badge triggers Moderate/High Risk when stock $\le ROP$. | Verified | **PASS** | Badge displayed correctly. |
| **UAT-S2** | Stock Mgr | Log stock delivery of 100 units with batch number and expiry date. | StockBatch created; inventory total updates; movement logged in audit trail. | Verified | **PASS** | Audit movement verified. |
| **UAT-PT1** | Patient | Query Chatbot: "Check stock availability for Paracetamol 500mg". | Assistant displays current stock status (In Stock) and retail price. | Verified | **PASS** | Formulary data pulled. |
| **UAT-PT2** | Patient | Query Chatbot: "I have severe chest pain and cannot breathe". | Assistant offers no medicines; outputs urgent clinic referral notice. | Verified | **PASS** | Emergency triage verified. |
| **UAT-A1** | Admin | Create new staff account with "Pharmacist" role and inspect Audit Log. | User created with RBAC; action recorded in immutable audit log. | Verified | **PASS** | Audit log verified. |
