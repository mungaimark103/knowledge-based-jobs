<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import {
    X,
    CheckCircle2,
    ArrowRight,
    ArrowLeft,
    ShieldCheck,
    FileText,
    UserCheck,
    Award,
    Briefcase,
    Plus,
    Users,
    AlertCircle,
    List,
    ListOrdered,
} from '@lucide/vue';
import { ref, computed } from 'vue';

const props = defineProps<{
    show: boolean;
    job: {
        id: number;
        title: string;
        organization: string;
        grade: string;
        min_experience: number;
    };
    candidateProfile?: any;
}>();

const emit = defineEmits(['close', 'submitted']);

const currentStep = ref(1);

const monthsList = [
    { value: '01', name: 'Jan' },
    { value: '02', name: 'Feb' },
    { value: '03', name: 'Mar' },
    { value: '04', name: 'Apr' },
    { value: '05', name: 'May' },
    { value: '06', name: 'Jun' },
    { value: '07', name: 'Jul' },
    { value: '08', name: 'Aug' },
    { value: '09', name: 'Sep' },
    { value: '10', name: 'Oct' },
    { value: '11', name: 'Nov' },
    { value: '12', name: 'Dec' },
];

const currentYearNum = new Date().getFullYear();
const yearsList = Array.from({ length: 35 }, (_, i) =>
    String(currentYearNum - i),
);

function getPositionTimestamp(w: any, isStart = false): number {
    if (!isStart && w.is_current) {
        return 99999999;
    }

    const year = isStart ? w.start_year || '1970' : w.end_year || '1970';
    const month = isStart ? w.start_month || '01' : w.end_month || '01';

    return parseInt(year) * 100 + parseInt(month);
}

function sortWorkHistoryReverseChronological(items: any[]): any[] {
    return [...items].sort((a, b) => {
        const endA = getPositionTimestamp(a, false);
        const endB = getPositionTimestamp(b, false);

        if (endB !== endA) {
            return endB - endA;
        }

        const startA = getPositionTimestamp(a, true);
        const startB = getPositionTimestamp(b, true);

        return startB - startA;
    });
}

function hasInvalidDates(w: any): boolean {
    if (
        w.is_current ||
        !w.start_year ||
        !w.start_month ||
        !w.end_year ||
        !w.end_month
    ) {
        return false;
    }

    const sDate = new Date(
        parseInt(w.start_year),
        parseInt(w.start_month) - 1,
        1,
    );
    const eDate = new Date(parseInt(w.end_year), parseInt(w.end_month) - 1, 1);

    return sDate > eDate;
}

function isValidPhone(phone?: string): boolean {
    if (!phone || phone.trim() === '') {
        return true;
    }

    return /^\+?[0-9\s\-\(\)]{7,20}$/.test(phone.trim());
}

function isValidEmail(email?: string): boolean {
    if (!email || email.trim() === '') {
        return true;
    }

    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.trim());
}

const form = useForm({
    screening_answers: {
        degree: '',
        experience: '',
        language: '',
        disciplinary: '',
        deployment: '',
    },
    education_history_data:
        Array.isArray(props.candidateProfile?.education_history) &&
        props.candidateProfile.education_history.length
            ? props.candidateProfile.education_history.map((e: any) => ({
                  degree: e.degree || '',
                  institution: e.institution || '',
                  specialization: e.specialization || '',
                  year: e.year || String(currentYearNum - 4),
              }))
            : [
                  {
                      degree:
                          props.candidateProfile?.education_level ||
                          "Bachelor's Degree",
                      institution: 'University / Tertiary Institution',
                      specialization: 'General Studies',
                      year: String(currentYearNum - 4),
                  },
              ],
    work_history_data:
        Array.isArray(props.candidateProfile?.work_history) &&
        props.candidateProfile.work_history.length
            ? sortWorkHistoryReverseChronological(
                  props.candidateProfile.work_history.map((w: any) => ({
                      role: w.role || '',
                      employer: w.employer || '',
                      start_month: w.start_month || '01',
                      start_year: w.start_year || String(currentYearNum - 2),
                      is_current:
                          w.is_current !== undefined
                              ? Boolean(w.is_current)
                              : true,
                      end_month: w.end_month || '12',
                      end_year: w.end_year || String(currentYearNum),
                      description: w.description || '',
                  })),
              )
            : [
                  {
                      role: '',
                      employer: '',
                      start_month: '01',
                      start_year: String(currentYearNum - 2),
                      is_current: true,
                      end_month: '12',
                      end_year: String(currentYearNum),
                      description: '',
                  },
              ],
    references_data:
        Array.isArray(props.candidateProfile?.references_list) &&
        props.candidateProfile.references_list.length
            ? props.candidateProfile.references_list.map((r: any) => ({
                  name: r.name || '',
                  title: r.title || '',
                  organization: r.organization || '',
                  email: r.email || '',
                  phone: r.phone || '',
              }))
            : [
                  {
                      name: '',
                      title: '',
                      organization: '',
                      email: '',
                      phone: '',
                  },
                  {
                      name: '',
                      title: '',
                      organization: '',
                      email: '',
                      phone: '',
                  },
              ],
    motivational_statement: '',
    integrity_accepted: false,
    ai_declaration_accepted: false,
});

const dateValidationError = computed(() => {
    return (form.work_history_data || []).some((w) => hasInvalidDates(w));
});

const stepErrors = computed(() => {
    const errs: string[] = [];

    if (currentStep.value === 1) {
        if (!form.screening_answers.degree) {
            errs.push(
                'Please answer Question 1 (Advanced university degree requirement).',
            );
        }

        if (!form.screening_answers.experience) {
            errs.push(
                'Please answer Question 2 (Minimum required experience).',
            );
        }

        if (!form.screening_answers.language) {
            errs.push(
                'Please answer Question 3 (Language fluency requirement).',
            );
        }

        if (!form.screening_answers.disciplinary) {
            errs.push(
                'Please answer Question 4 (Disciplinary history declaration).',
            );
        }

        if (!form.screening_answers.deployment) {
            errs.push(
                'Please answer Question 5 (Field deployment willingness).',
            );
        }
    } else if (currentStep.value === 2) {
        const validEdu = form.education_history_data.filter(
            (e: any) => e.degree.trim() !== '' || e.institution.trim() !== '',
        );

        if (validEdu.length === 0) {
            errs.push(
                'Please provide at least one education qualification (Degree or Institution).',
            );
        }
    } else if (currentStep.value === 3) {
        const validWork = form.work_history_data.filter(
            (w: any) => w.role.trim() !== '' || w.employer.trim() !== '',
        );

        if (validWork.length === 0) {
            errs.push(
                'Please provide at least one work experience position (Role or Employer).',
            );
        }

        if (dateValidationError.value) {
            errs.push(
                'One or more work positions have a Start Date later than the End Date.',
            );
        }

        const validRefs = form.references_data.filter(
            (r: any) => r.name.trim() !== '',
        );

        if (validRefs.length < 2) {
            errs.push(
                'Please provide at least two professional references with full names.',
            );
        }

        form.references_data.forEach((r: any, idx: number) => {
            if (r.name.trim() !== '') {
                if (r.phone && !isValidPhone(r.phone)) {
                    errs.push(
                        `Referee #${idx + 1} (${r.name}) phone number format is invalid (e.g. +254 700 000 000).`,
                    );
                }

                if (r.email && !isValidEmail(r.email)) {
                    errs.push(
                        `Referee #${idx + 1} (${r.name}) email address format is invalid.`,
                    );
                }
            }
        });
    } else if (currentStep.value === 4) {
        if (form.motivational_statement.trim().length < 50) {
            errs.push(
                'Motivational statement must be at least 50 characters long.',
            );
        }
    } else if (currentStep.value === 5) {
        if (!form.integrity_accepted) {
            errs.push('You must accept the Truthfulness Certification.');
        }

        if (!form.ai_declaration_accepted) {
            errs.push('You must accept the AI Non-Fabrication Confirmation.');
        }
    }

    return errs;
});

const canGoNext = computed(() => stepErrors.value.length === 0);

function addWorkExperience() {
    form.work_history_data.push({
        role: '',
        employer: '',
        start_month: '01',
        start_year: String(currentYearNum - 1),
        is_current: false,
        end_month: '12',
        end_year: String(currentYearNum),
        description: '',
    });
}

function removeWorkExperience(index: number) {
    if (form.work_history_data.length > 1) {
        form.work_history_data.splice(index, 1);
    }
}

function addEducationItem() {
    form.education_history_data.push({
        degree: '',
        institution: '',
        specialization: '',
        year: String(currentYearNum - 4),
    });
}

function removeEducationItem(index: number) {
    if (form.education_history_data.length > 1) {
        form.education_history_data.splice(index, 1);
    }
}

function addReferenceItem() {
    form.references_data.push({
        name: '',
        title: '',
        organization: '',
        email: '',
        phone: '',
    });
}

function removeReferenceItem(index: number) {
    if (form.references_data.length > 1) {
        form.references_data.splice(index, 1);
    }
}

function insertBullets(index: number) {
    const item = form.work_history_data[index];

    if (!item) {
        return;
    }

    if (!item.description || item.description.trim() === '') {
        item.description = '• ';
    } else {
        const lines = item.description.split('\n');
        item.description = lines
            .map((line: string) =>
                line.trim().startsWith('• ') || /^\d+\.\s/.test(line.trim())
                    ? line
                    : `• ${line}`,
            )
            .join('\n');
    }
}

function insertNumbers(index: number) {
    const item = form.work_history_data[index];

    if (!item) {
        return;
    }

    if (!item.description || item.description.trim() === '') {
        item.description = '1. ';
    } else {
        const lines = item.description.split('\n');
        item.description = lines
            .map((line: string, i: number) => {
                const clean = line.replace(/^[•\d\.\s]+/, '').trim();

                return clean ? `${i + 1}. ${clean}` : `${i + 1}. `;
            })
            .join('\n');
    }
}

function nextStep() {
    if (canGoNext.value && currentStep.value < 5) {
        currentStep.value++;
    }
}

function prevStep() {
    if (currentStep.value > 1) {
        currentStep.value--;
    }
}

function submitApplication() {
    if (!canGoNext.value) {
        return;
    }

    const sortedWork = sortWorkHistoryReverseChronological(
        form.work_history_data
            .filter((w: any) => w.role.trim() !== '' || w.employer.trim() !== '')
            .map((w: any) => {
                const startLabel = `${monthsList.find((m) => m.value === w.start_month)?.name || w.start_month} ${w.start_year}`;
                const endLabel = w.is_current
                    ? 'Present'
                    : `${monthsList.find((m) => m.value === w.end_month)?.name || w.end_month} ${w.end_year}`;

                return {
                    ...w,
                    period: `${startLabel} – ${endLabel}`,
                };
            }),
    );

    form.transform((data) => ({
        ...data,
        work_history_data: sortedWork,
        education_history_data: data.education_history_data.filter(
            (e: any) => e.degree.trim() !== '' || e.institution.trim() !== '',
        ),
        references_data: data.references_data.filter(
            (r: any) => r.name.trim() !== '',
        ),
    })).post(`/opportunities/${props.job.id}/apply`, {
        preserveScroll: true,
        onSuccess: () => {
            currentStep.value = 6;
            emit('submitted');
        },
    });
}
</script>

<template>
    <div
        v-if="show"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-sm"
    >
        <div
            class="relative flex max-h-[90vh] w-full max-w-2xl flex-col space-y-5 rounded-3xl border border-slate-200 bg-white p-6 shadow-2xl dark:border-slate-800 dark:bg-slate-900"
        >
            <!-- Close Button -->
            <button
                @click="$emit('close')"
                class="absolute top-5 right-5 p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200"
            >
                <X class="h-5 w-5" />
            </button>

            <!-- Header -->
            <div
                class="flex items-center space-x-3 border-b border-slate-100 pb-3 dark:border-slate-800"
            >
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#00b2e3] font-bold text-white"
                >
                    <ShieldCheck class="h-6 w-6" />
                </div>
                <div>
                    <span
                        class="text-[10px] font-bold tracking-wider text-[#00b2e3] uppercase"
                        >{{ job.organization }} • {{ job.grade }}</span
                    >
                    <h3
                        class="text-base font-bold text-slate-900 dark:text-slate-100"
                    >
                        Application Wizard: {{ job.title }}
                    </h3>
                </div>
            </div>

            <!-- Wizard Progress Indicator -->
            <div
                v-if="currentStep <= 5"
                class="flex items-center justify-between border-b border-slate-100 pb-3 text-xs font-semibold text-slate-400 dark:border-slate-800"
            >
                <div
                    :class="[
                        'flex items-center gap-1.5',
                        currentStep >= 1 ? 'font-bold text-[#00b2e3]' : '',
                    ]"
                >
                    <span>1. Screening</span>
                </div>
                <div
                    :class="[
                        'flex items-center gap-1.5',
                        currentStep >= 2 ? 'font-bold text-[#00b2e3]' : '',
                    ]"
                >
                    <span>2. Education</span>
                </div>
                <div
                    :class="[
                        'flex items-center gap-1.5',
                        currentStep >= 3 ? 'font-bold text-[#00b2e3]' : '',
                    ]"
                >
                    <span>3. Experience</span>
                </div>
                <div
                    :class="[
                        'flex items-center gap-1.5',
                        currentStep >= 4 ? 'font-bold text-[#00b2e3]' : '',
                    ]"
                >
                    <span>4. Statement</span>
                </div>
                <div
                    :class="[
                        'flex items-center gap-1.5',
                        currentStep >= 5 ? 'font-bold text-[#00b2e3]' : '',
                    ]"
                >
                    <span>5. Consent</span>
                </div>
            </div>

            <!-- Scrollable Content Body -->
            <div class="flex-1 space-y-4 overflow-y-auto pr-1">
                <!-- Validation Error Summary Banner -->
                <div
                    v-if="stepErrors.length > 0"
                    class="space-y-1 rounded-2xl border border-rose-200 bg-rose-50 p-3.5 text-xs text-rose-700 dark:border-rose-800/60 dark:bg-rose-950/40 dark:text-rose-300"
                >
                    <div
                        class="flex items-center gap-1.5 font-bold text-rose-800 dark:text-rose-200"
                    >
                        <AlertCircle class="h-4 w-4 shrink-0 text-rose-600" />
                        Please complete the required items for Step
                        {{ currentStep }}:
                    </div>
                    <ul
                        class="list-inside list-disc space-y-0.5 pl-1 text-[11px]"
                    >
                        <li v-for="(err, idx) in stepErrors" :key="idx">
                            {{ err }}
                        </li>
                    </ul>
                </div>

                <!-- STEP 1: Mandatory Screening Questionnaire -->
                <div v-if="currentStep === 1" class="space-y-4 text-xs">
                    <h4
                        class="flex items-center gap-2 text-sm font-bold text-slate-900 dark:text-slate-100"
                    >
                        <UserCheck class="h-4 w-4 text-[#00b2e3]" /> Step 1:
                        Mandatory Eligibility Screening
                    </h4>
                    <p class="text-slate-500">
                        Answer the following required threshold screening
                        questions:
                    </p>

                    <div class="space-y-3">
                        <div
                            class="space-y-2 rounded-xl border border-slate-200 bg-slate-50 p-3.5 dark:border-slate-800 dark:bg-slate-950"
                        >
                            <label
                                class="font-semibold text-slate-800 dark:text-slate-200"
                                >1. Do you possess an advanced university degree
                                (Master's or equivalent) in a relevant
                                field?</label
                            >
                            <div class="flex items-center gap-4">
                                <label class="flex items-center gap-1.5"
                                    ><input
                                        type="radio"
                                        v-model="form.screening_answers.degree"
                                        value="yes"
                                    />
                                    Yes</label
                                >
                                <label class="flex items-center gap-1.5"
                                    ><input
                                        type="radio"
                                        v-model="form.screening_answers.degree"
                                        value="no"
                                    />
                                    No</label
                                >
                            </div>
                        </div>

                        <div
                            class="space-y-2 rounded-xl border border-slate-200 bg-slate-50 p-3.5 dark:border-slate-800 dark:bg-slate-950"
                        >
                            <label
                                class="font-semibold text-slate-800 dark:text-slate-200"
                                >2. Do you have at least
                                {{ job.min_experience }} years of relevant
                                professional experience?</label
                            >
                            <div class="flex items-center gap-4">
                                <label class="flex items-center gap-1.5"
                                    ><input
                                        type="radio"
                                        v-model="
                                            form.screening_answers.experience
                                        "
                                        value="yes"
                                    />
                                    Yes</label
                                >
                                <label class="flex items-center gap-1.5"
                                    ><input
                                        type="radio"
                                        v-model="
                                            form.screening_answers.experience
                                        "
                                        value="no"
                                    />
                                    No</label
                                >
                            </div>
                        </div>

                        <div
                            class="space-y-2 rounded-xl border border-slate-200 bg-slate-50 p-3.5 dark:border-slate-800 dark:bg-slate-950"
                        >
                            <label
                                class="font-semibold text-slate-800 dark:text-slate-200"
                                >3. Do you possess fluency in the required
                                official working languages?</label
                            >
                            <div class="flex items-center gap-4">
                                <label class="flex items-center gap-1.5"
                                    ><input
                                        type="radio"
                                        v-model="
                                            form.screening_answers.language
                                        "
                                        value="yes"
                                    />
                                    Yes</label
                                >
                                <label class="flex items-center gap-1.5"
                                    ><input
                                        type="radio"
                                        v-model="
                                            form.screening_answers.language
                                        "
                                        value="no"
                                    />
                                    No</label
                                >
                            </div>
                        </div>

                        <div
                            class="space-y-2 rounded-xl border border-slate-200 bg-slate-50 p-3.5 dark:border-slate-800 dark:bg-slate-950"
                        >
                            <label
                                class="font-semibold text-slate-800 dark:text-slate-200"
                                >4. Have you ever been subject to criminal
                                conviction or administrative disciplinary
                                action?</label
                            >
                            <div class="flex items-center gap-4">
                                <label class="flex items-center gap-1.5"
                                    ><input
                                        type="radio"
                                        v-model="
                                            form.screening_answers.disciplinary
                                        "
                                        value="no"
                                    />
                                    No</label
                                >
                                <label class="flex items-center gap-1.5"
                                    ><input
                                        type="radio"
                                        v-model="
                                            form.screening_answers.disciplinary
                                        "
                                        value="yes"
                                    />
                                    Yes</label
                                >
                            </div>
                        </div>

                        <div
                            class="space-y-2 rounded-xl border border-slate-200 bg-slate-50 p-3.5 dark:border-slate-800 dark:bg-slate-950"
                        >
                            <label
                                class="font-semibold text-slate-800 dark:text-slate-200"
                                >5. Are you willing to be deployed to field duty
                                stations if required?</label
                            >
                            <div class="flex items-center gap-4">
                                <label class="flex items-center gap-1.5"
                                    ><input
                                        type="radio"
                                        v-model="
                                            form.screening_answers.deployment
                                        "
                                        value="yes"
                                    />
                                    Yes</label
                                >
                                <label class="flex items-center gap-1.5"
                                    ><input
                                        type="radio"
                                        v-model="
                                            form.screening_answers.deployment
                                        "
                                        value="no"
                                    />
                                    No</label
                                >
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STEP 2: Educational Background -->
                <div v-if="currentStep === 2" class="space-y-4 text-xs">
                    <div class="flex items-center justify-between">
                        <h4
                            class="flex items-center gap-2 text-sm font-bold text-slate-900 dark:text-slate-100"
                        >
                            <Award class="h-4 w-4 text-[#00b2e3]" /> Step 2:
                            Educational Qualifications
                        </h4>
                        <button
                            type="button"
                            @click="addEducationItem"
                            class="inline-flex items-center gap-1 rounded-lg bg-indigo-500/10 px-3 py-1 text-[11px] font-bold text-indigo-600 transition hover:bg-indigo-500/20 dark:text-indigo-400"
                        >
                            <Plus class="h-3.5 w-3.5" /> Add Qualification
                        </button>
                    </div>

                    <div class="space-y-3">
                        <div
                            v-for="(
                                eItem, index
                            ) in form.education_history_data"
                            :key="index"
                            class="relative space-y-3 rounded-2xl border border-slate-200 bg-slate-50 p-3.5 dark:border-slate-800 dark:bg-slate-950"
                        >
                            <div class="flex items-center justify-between">
                                <span
                                    class="font-mono text-[10px] font-bold text-slate-400"
                                    >QUALIFICATION #{{ index + 1 }}</span
                                >
                                <button
                                    v-if="
                                        form.education_history_data.length > 1
                                    "
                                    type="button"
                                    @click="removeEducationItem(index)"
                                    class="text-[11px] font-bold text-rose-500 hover:text-rose-600"
                                >
                                    Remove
                                </button>
                            </div>

                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <div>
                                    <label
                                        class="mb-0.5 block text-[11px] font-semibold text-slate-600 dark:text-slate-400"
                                        >Degree / Qualification</label
                                    >
                                    <input
                                        v-model="eItem.degree"
                                        type="text"
                                        placeholder="e.g. Master of Science (M.Sc.)"
                                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs dark:border-slate-800 dark:bg-slate-900"
                                    />
                                </div>
                                <div>
                                    <label
                                        class="mb-0.5 block text-[11px] font-semibold text-slate-600 dark:text-slate-400"
                                        >Academic Institution /
                                        University</label
                                    >
                                    <input
                                        v-model="eItem.institution"
                                        type="text"
                                        placeholder="e.g. University of Nairobi"
                                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs dark:border-slate-800 dark:bg-slate-900"
                                    />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <div>
                                    <label
                                        class="mb-0.5 block text-[11px] font-semibold text-slate-600 dark:text-slate-400"
                                        >Specialization / Major</label
                                    >
                                    <input
                                        v-model="eItem.specialization"
                                        type="text"
                                        placeholder="e.g. Computer Science / Public Policy"
                                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs dark:border-slate-800 dark:bg-slate-900"
                                    />
                                </div>
                                <div>
                                    <label
                                        class="mb-0.5 block text-[11px] font-semibold text-slate-600 dark:text-slate-400"
                                        >Graduation Year</label
                                    >
                                    <select
                                        v-model="eItem.year"
                                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium dark:border-slate-800 dark:bg-slate-900"
                                    >
                                        <option
                                            v-for="y in yearsList"
                                            :key="y"
                                            :value="y"
                                        >
                                            {{ y }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STEP 3: Work History & References -->
                <div v-if="currentStep === 3" class="space-y-5 text-xs">
                    <!-- Work Positions List Section -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <h4
                                class="flex items-center gap-1.5 text-sm font-bold text-slate-900 dark:text-slate-100"
                            >
                                <Briefcase class="h-4 w-4 text-[#00b2e3]" />
                                Step 3: Work History (Multiple Positions)
                            </h4>
                            <button
                                type="button"
                                @click="addWorkExperience"
                                class="inline-flex items-center gap-1 rounded-lg bg-[#00b2e3]/10 px-3 py-1 text-[11px] font-bold text-[#00b2e3] transition hover:bg-[#00b2e3]/20"
                            >
                                <Plus class="h-3.5 w-3.5" /> Add Work Position
                            </button>
                        </div>

                        <div
                            v-for="(wItem, index) in form.work_history_data"
                            :key="index"
                            class="relative space-y-3 rounded-2xl border border-slate-200 bg-slate-50 p-3.5 dark:border-slate-800 dark:bg-slate-950"
                        >
                            <div class="flex items-center justify-between">
                                <span
                                    class="font-mono text-[10px] font-bold text-slate-400"
                                    >POSITION #{{ index + 1 }}</span
                                >
                                <button
                                    v-if="form.work_history_data.length > 1"
                                    type="button"
                                    @click="removeWorkExperience(index)"
                                    class="text-[11px] font-bold text-rose-500 hover:text-rose-600"
                                >
                                    Remove
                                </button>
                            </div>

                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <div>
                                    <label
                                        class="mb-0.5 block text-[11px] font-semibold text-slate-600 dark:text-slate-400"
                                        >Role Title</label
                                    >
                                    <input
                                        v-model="wItem.role"
                                        type="text"
                                        placeholder="e.g. Senior Software Engineer"
                                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs dark:border-slate-800 dark:bg-slate-900"
                                    />
                                </div>
                                <div>
                                    <label
                                        class="mb-0.5 block text-[11px] font-semibold text-slate-600 dark:text-slate-400"
                                        >Employer / Organization</label
                                    >
                                    <input
                                        v-model="wItem.employer"
                                        type="text"
                                        placeholder="e.g. Safaricom PLC / UN Agency"
                                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs dark:border-slate-800 dark:bg-slate-900"
                                    />
                                </div>
                            </div>

                            <!-- Structured Month & Year Calendar Selector -->
                            <div
                                class="space-y-2 rounded-xl border border-slate-200 bg-white p-3 dark:border-slate-800 dark:bg-slate-900"
                            >
                                <div
                                    class="grid grid-cols-1 gap-3 sm:grid-cols-2"
                                >
                                    <div>
                                        <label
                                            class="mb-1 block text-[11px] font-semibold text-slate-600 dark:text-slate-400"
                                            >Start Date (Month & Year)</label
                                        >
                                        <div class="flex gap-2">
                                            <select
                                                v-model="wItem.start_month"
                                                class="w-1/2 rounded-lg border border-slate-200 bg-slate-50 px-2 py-1 text-xs font-medium dark:border-slate-800 dark:bg-slate-950"
                                            >
                                                <option
                                                    v-for="m in monthsList"
                                                    :key="m.value"
                                                    :value="m.value"
                                                >
                                                    {{ m.name }}
                                                </option>
                                            </select>
                                            <select
                                                v-model="wItem.start_year"
                                                class="w-1/2 rounded-lg border border-slate-200 bg-slate-50 px-2 py-1 text-xs font-medium dark:border-slate-800 dark:bg-slate-950"
                                            >
                                                <option
                                                    v-for="y in yearsList"
                                                    :key="y"
                                                    :value="y"
                                                >
                                                    {{ y }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div>
                                        <div
                                            class="mb-1 flex items-center justify-between"
                                        >
                                            <label
                                                class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400"
                                                >End Date</label
                                            >
                                            <label
                                                class="inline-flex cursor-pointer items-center gap-1 text-[11px] font-bold text-[#00b2e3]"
                                            >
                                                <input
                                                    v-model="wItem.is_current"
                                                    type="checkbox"
                                                    class="rounded border-slate-300 text-[#00b2e3]"
                                                />
                                                Present
                                            </label>
                                        </div>

                                        <div
                                            v-if="!wItem.is_current"
                                            class="flex gap-2"
                                        >
                                            <select
                                                v-model="wItem.end_month"
                                                class="w-1/2 rounded-lg border border-slate-200 bg-slate-50 px-2 py-1 text-xs font-medium dark:border-slate-800 dark:bg-slate-950"
                                            >
                                                <option
                                                    v-for="m in monthsList"
                                                    :key="m.value"
                                                    :value="m.value"
                                                >
                                                    {{ m.name }}
                                                </option>
                                            </select>
                                            <select
                                                v-model="wItem.end_year"
                                                class="w-1/2 rounded-lg border border-slate-200 bg-slate-50 px-2 py-1 text-xs font-medium dark:border-slate-800 dark:bg-slate-950"
                                            >
                                                <option
                                                    v-for="y in yearsList"
                                                    :key="y"
                                                    :value="y"
                                                >
                                                    {{ y }}
                                                </option>
                                            </select>
                                        </div>
                                        <div
                                            v-else
                                            class="flex items-center justify-center rounded-lg bg-emerald-500/10 px-3 py-1 text-xs font-bold text-emerald-600 dark:text-emerald-400"
                                        >
                                            Currently Work Here (Present)
                                        </div>
                                    </div>
                                </div>

                                <div
                                    v-if="hasInvalidDates(wItem)"
                                    class="mt-2 flex items-center gap-1.5 rounded-lg border border-rose-500/20 bg-rose-500/10 p-2 text-[11px] font-bold text-rose-600 dark:text-rose-400"
                                >
                                    <AlertCircle class="h-3.5 w-3.5" /> Start
                                    date cannot be after End date!
                                </div>
                            </div>

                            <div>
                                <div
                                    class="mb-1 flex items-center justify-between"
                                >
                                    <label
                                        class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400"
                                        >Key Deliverables &
                                        Responsibilities</label
                                    >
                                    <div class="flex items-center space-x-1">
                                        <button
                                            type="button"
                                            @click="insertBullets(index)"
                                            class="flex items-center gap-1 rounded bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-700 transition hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700"
                                        >
                                            <List
                                                class="h-3 w-3 text-[#00b2e3]"
                                            />
                                            Bullet Points
                                        </button>
                                        <button
                                            type="button"
                                            @click="insertNumbers(index)"
                                            class="flex items-center gap-1 rounded bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-700 transition hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700"
                                        >
                                            <ListOrdered
                                                class="h-3 w-3 text-indigo-500"
                                            />
                                            Numbered List
                                        </button>
                                    </div>
                                </div>
                                <textarea
                                    v-model="wItem.description"
                                    rows="2"
                                    placeholder="Key responsibilities and achievements in this role..."
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs dark:border-slate-800 dark:bg-slate-900"
                                ></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Professional References Section -->
                    <div
                        class="space-y-3 border-t border-slate-100 pt-3 dark:border-slate-800"
                    >
                        <div class="flex items-center justify-between">
                            <h4
                                class="flex items-center gap-1.5 text-sm font-bold text-slate-900 dark:text-slate-100"
                            >
                                <Users class="h-4 w-4 text-emerald-500" />
                                Professional References (Min 2 Referees)
                            </h4>
                            <button
                                type="button"
                                @click="addReferenceItem"
                                class="inline-flex items-center gap-1 rounded-lg bg-emerald-500/10 px-3 py-1 text-[11px] font-bold text-emerald-600 transition hover:bg-emerald-500/20 dark:text-emerald-400"
                            >
                                <Plus class="h-3.5 w-3.5" /> Add Reference
                            </button>
                        </div>

                        <div
                            v-for="(rItem, index) in form.references_data"
                            :key="index"
                            class="relative space-y-3 rounded-2xl border border-slate-200 bg-slate-50 p-3.5 dark:border-slate-800 dark:bg-slate-950"
                        >
                            <div class="flex items-center justify-between">
                                <span
                                    class="font-mono text-[10px] font-bold text-slate-400"
                                    >REFEREE #{{ index + 1 }}</span
                                >
                                <button
                                    v-if="form.references_data.length > 2"
                                    type="button"
                                    @click="removeReferenceItem(index)"
                                    class="text-[11px] font-bold text-rose-500 hover:text-rose-600"
                                >
                                    Remove
                                </button>
                            </div>

                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <div>
                                    <label
                                        class="mb-0.5 block text-[11px] font-semibold text-slate-600 dark:text-slate-400"
                                        >Referee Full Name</label
                                    >
                                    <input
                                        v-model="rItem.name"
                                        type="text"
                                        placeholder="e.g. Dr. Jane Doe"
                                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs dark:border-slate-800 dark:bg-slate-900"
                                    />
                                </div>
                                <div>
                                    <label
                                        class="mb-0.5 block text-[11px] font-semibold text-slate-600 dark:text-slate-400"
                                        >Job Title</label
                                    >
                                    <input
                                        v-model="rItem.title"
                                        type="text"
                                        placeholder="e.g. Director of Technology"
                                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs dark:border-slate-800 dark:bg-slate-900"
                                    />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                                <div>
                                    <label
                                        class="mb-0.5 block text-[11px] font-semibold text-slate-600 dark:text-slate-400"
                                        >Organization</label
                                    >
                                    <input
                                        v-model="rItem.organization"
                                        type="text"
                                        placeholder="e.g. Safaricom PLC"
                                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs dark:border-slate-800 dark:bg-slate-900"
                                    />
                                </div>
                                <div>
                                    <label
                                        class="mb-0.5 block text-[11px] font-semibold text-slate-600 dark:text-slate-400"
                                        >Email Address</label
                                    >
                                    <input
                                        v-model="rItem.email"
                                        type="email"
                                        placeholder="jane.doe@example.com"
                                        :class="[
                                            'w-full rounded-xl border bg-white px-3 py-1.5 text-xs transition dark:bg-slate-900',
                                            !isValidEmail(rItem.email)
                                                ? 'border-rose-500 text-rose-600 dark:text-rose-400'
                                                : 'border-slate-200 dark:border-slate-800',
                                        ]"
                                    />
                                    <span
                                        v-if="!isValidEmail(rItem.email)"
                                        class="block pt-0.5 text-[10px] font-bold text-rose-500"
                                        >Invalid email format</span
                                    >
                                </div>
                                <div>
                                    <label
                                        class="mb-0.5 block text-[11px] font-semibold text-slate-600 dark:text-slate-400"
                                        >Phone Number</label
                                    >
                                    <input
                                        v-model="rItem.phone"
                                        type="text"
                                        placeholder="+254 700 000 000"
                                        :class="[
                                            'w-full rounded-xl border bg-white px-3 py-1.5 text-xs transition dark:bg-slate-900',
                                            !isValidPhone(rItem.phone)
                                                ? 'border-rose-500 text-rose-600 dark:text-rose-400'
                                                : 'border-slate-200 dark:border-slate-800',
                                        ]"
                                    />
                                    <span
                                        v-if="!isValidPhone(rItem.phone)"
                                        class="block pt-0.5 text-[10px] font-bold text-rose-500"
                                        >Invalid phone format</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STEP 4: Motivational Statement -->
                <div v-if="currentStep === 4" class="space-y-4 text-xs">
                    <h4
                        class="flex items-center gap-2 text-sm font-bold text-slate-900 dark:text-slate-100"
                    >
                        <FileText class="h-4 w-4 text-[#00b2e3]" /> Step 4:
                        Motivational Statement / Cover Letter
                    </h4>
                    <p class="text-slate-500">
                        Provide your statement of interest (Copy-paste plain
                        text, minimum 50 characters required):
                    </p>

                    <div>
                        <textarea
                            v-model="form.motivational_statement"
                            rows="6"
                            placeholder="Explain why your experience matches this position and what contributions you will bring..."
                            class="w-full rounded-2xl border border-slate-200 bg-slate-50 p-3 focus:ring-2 focus:ring-[#00b2e3]/20 focus:outline-none dark:border-slate-800 dark:bg-slate-950"
                        ></textarea>
                        <div class="mt-1 text-right text-[11px] text-slate-400">
                            {{ form.motivational_statement.trim().length }} / 50
                            characters min
                        </div>
                    </div>
                </div>

                <!-- STEP 5: Integrity Declaration & AI Non-Fabrication Consent -->
                <div v-if="currentStep === 5" class="space-y-4 text-xs">
                    <h4
                        class="flex items-center gap-2 text-sm font-bold text-slate-900 dark:text-slate-100"
                    >
                        <ShieldCheck class="h-4 w-4 text-emerald-500" /> Step 5:
                        Integrity & Verification Declarations
                    </h4>

                    <div
                        class="space-y-3 rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950"
                    >
                        <label class="flex cursor-pointer items-start gap-3">
                            <input
                                type="checkbox"
                                v-model="form.integrity_accepted"
                                class="mt-1 rounded text-[#00b2e3]"
                            />
                            <span
                                class="leading-relaxed text-slate-700 dark:text-slate-300"
                            >
                                <strong>Truthfulness Certification:</strong> I
                                certify that all statements made in this
                                application are true, complete, and correct to
                                the best of my knowledge and belief.
                            </span>
                        </label>

                        <label
                            class="flex cursor-pointer items-start gap-3 border-t border-slate-200 pt-3 dark:border-slate-800"
                        >
                            <input
                                type="checkbox"
                                v-model="form.ai_declaration_accepted"
                                class="mt-1 rounded text-[#00b2e3]"
                            />
                            <span
                                class="leading-relaxed text-slate-700 dark:text-slate-300"
                            >
                                <strong
                                    >AI Non-Fabrication Confirmation:</strong
                                >
                                I confirm that I have NOT used automated AI
                                tools to fabricate credentials, work history, or
                                unverified experience claims.
                            </span>
                        </label>
                    </div>
                </div>

                <!-- STEP 6: Submission Success -->
                <div
                    v-if="currentStep === 6"
                    class="space-y-4 py-8 text-center"
                >
                    <div
                        class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-500/10 font-bold text-emerald-500"
                    >
                        <CheckCircle2 class="h-8 w-8" />
                    </div>
                    <h3
                        class="text-xl font-bold text-slate-900 dark:text-slate-100"
                    >
                        Application Submitted & Evaluated!
                    </h3>
                    <p
                        class="mx-auto max-w-md text-xs leading-relaxed text-slate-500"
                    >
                        Your application for
                        <strong>{{ job.title }}</strong> has been recorded. A
                        formal confirmation email notification has been sent to
                        your registered email address.
                    </p>
                    <button
                        @click="$emit('close')"
                        class="rounded-xl bg-[#00b2e3] px-6 py-2.5 text-xs font-semibold text-white hover:bg-[#0099c4]"
                    >
                        Close Window
                    </button>
                </div>
            </div>

            <!-- Controls -->
            <div
                v-if="currentStep <= 5"
                class="flex items-center justify-between border-t border-slate-100 pt-3 dark:border-slate-800"
            >
                <button
                    v-if="currentStep > 1"
                    @click="prevStep"
                    class="flex items-center gap-1 rounded-xl bg-slate-100 px-4 py-2 text-xs font-semibold text-slate-700 dark:bg-slate-800 dark:text-slate-300"
                >
                    <ArrowLeft class="h-4 w-4" /> Back
                </button>
                <div v-else></div>

                <button
                    v-if="currentStep < 5"
                    @click="nextStep"
                    :disabled="!canGoNext"
                    class="flex items-center gap-1.5 rounded-xl bg-[#00b2e3] px-5 py-2.5 text-xs font-semibold text-white transition hover:bg-[#0099c4] disabled:opacity-40"
                >
                    Next Step <ArrowRight class="h-4 w-4" />
                </button>

                <button
                    v-if="currentStep === 5"
                    @click="submitApplication"
                    :disabled="!canGoNext || form.processing"
                    class="flex items-center gap-1.5 rounded-xl bg-emerald-600 px-6 py-2.5 text-xs font-semibold text-white shadow-sm transition hover:bg-emerald-700 disabled:opacity-40"
                >
                    Submit Application <CheckCircle2 class="h-4 w-4" />
                </button>
            </div>
        </div>
    </div>
</template>
