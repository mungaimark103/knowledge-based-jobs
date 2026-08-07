import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\AgencyDashboardController::index
* @see app/Http/Controllers/AgencyDashboardController.php:20
* @route '/admin/dashboard'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/dashboard',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AgencyDashboardController::index
* @see app/Http/Controllers/AgencyDashboardController.php:20
* @route '/admin/dashboard'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AgencyDashboardController::index
* @see app/Http/Controllers/AgencyDashboardController.php:20
* @route '/admin/dashboard'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AgencyDashboardController::index
* @see app/Http/Controllers/AgencyDashboardController.php:20
* @route '/admin/dashboard'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\AgencyDashboardController::index
* @see app/Http/Controllers/AgencyDashboardController.php:20
* @route '/admin/dashboard'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AgencyDashboardController::index
* @see app/Http/Controllers/AgencyDashboardController.php:20
* @route '/admin/dashboard'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AgencyDashboardController::index
* @see app/Http/Controllers/AgencyDashboardController.php:20
* @route '/admin/dashboard'
*/
indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

/**
* @see \App\Http\Controllers\AgencyDashboardController::toggleVerifyOrganization
* @see app/Http/Controllers/AgencyDashboardController.php:121
* @route '/admin/organizations/{id}/verify'
*/
export const toggleVerifyOrganization = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: toggleVerifyOrganization.url(args, options),
    method: 'patch',
})

toggleVerifyOrganization.definition = {
    methods: ["patch"],
    url: '/admin/organizations/{id}/verify',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\AgencyDashboardController::toggleVerifyOrganization
* @see app/Http/Controllers/AgencyDashboardController.php:121
* @route '/admin/organizations/{id}/verify'
*/
toggleVerifyOrganization.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
    }

    return toggleVerifyOrganization.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AgencyDashboardController::toggleVerifyOrganization
* @see app/Http/Controllers/AgencyDashboardController.php:121
* @route '/admin/organizations/{id}/verify'
*/
toggleVerifyOrganization.patch = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: toggleVerifyOrganization.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\AgencyDashboardController::toggleVerifyOrganization
* @see app/Http/Controllers/AgencyDashboardController.php:121
* @route '/admin/organizations/{id}/verify'
*/
const toggleVerifyOrganizationForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: toggleVerifyOrganization.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AgencyDashboardController::toggleVerifyOrganization
* @see app/Http/Controllers/AgencyDashboardController.php:121
* @route '/admin/organizations/{id}/verify'
*/
toggleVerifyOrganizationForm.patch = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: toggleVerifyOrganization.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

toggleVerifyOrganization.form = toggleVerifyOrganizationForm

/**
* @see \App\Http\Controllers\AgencyDashboardController::toggleVerifyCandidate
* @see app/Http/Controllers/AgencyDashboardController.php:139
* @route '/admin/candidates/{id}/verify'
*/
export const toggleVerifyCandidate = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: toggleVerifyCandidate.url(args, options),
    method: 'patch',
})

toggleVerifyCandidate.definition = {
    methods: ["patch"],
    url: '/admin/candidates/{id}/verify',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\AgencyDashboardController::toggleVerifyCandidate
* @see app/Http/Controllers/AgencyDashboardController.php:139
* @route '/admin/candidates/{id}/verify'
*/
toggleVerifyCandidate.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
    }

    return toggleVerifyCandidate.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AgencyDashboardController::toggleVerifyCandidate
* @see app/Http/Controllers/AgencyDashboardController.php:139
* @route '/admin/candidates/{id}/verify'
*/
toggleVerifyCandidate.patch = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: toggleVerifyCandidate.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\AgencyDashboardController::toggleVerifyCandidate
* @see app/Http/Controllers/AgencyDashboardController.php:139
* @route '/admin/candidates/{id}/verify'
*/
const toggleVerifyCandidateForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: toggleVerifyCandidate.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AgencyDashboardController::toggleVerifyCandidate
* @see app/Http/Controllers/AgencyDashboardController.php:139
* @route '/admin/candidates/{id}/verify'
*/
toggleVerifyCandidateForm.patch = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: toggleVerifyCandidate.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

toggleVerifyCandidate.form = toggleVerifyCandidateForm

/**
* @see \App\Http\Controllers\AgencyDashboardController::createProxyCandidate
* @see app/Http/Controllers/AgencyDashboardController.php:159
* @route '/admin/candidates/proxy'
*/
export const createProxyCandidate = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: createProxyCandidate.url(options),
    method: 'post',
})

createProxyCandidate.definition = {
    methods: ["post"],
    url: '/admin/candidates/proxy',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AgencyDashboardController::createProxyCandidate
* @see app/Http/Controllers/AgencyDashboardController.php:159
* @route '/admin/candidates/proxy'
*/
createProxyCandidate.url = (options?: RouteQueryOptions) => {
    return createProxyCandidate.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AgencyDashboardController::createProxyCandidate
* @see app/Http/Controllers/AgencyDashboardController.php:159
* @route '/admin/candidates/proxy'
*/
createProxyCandidate.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: createProxyCandidate.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AgencyDashboardController::createProxyCandidate
* @see app/Http/Controllers/AgencyDashboardController.php:159
* @route '/admin/candidates/proxy'
*/
const createProxyCandidateForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: createProxyCandidate.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AgencyDashboardController::createProxyCandidate
* @see app/Http/Controllers/AgencyDashboardController.php:159
* @route '/admin/candidates/proxy'
*/
createProxyCandidateForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: createProxyCandidate.url(options),
    method: 'post',
})

createProxyCandidate.form = createProxyCandidateForm

/**
* @see \App\Http\Controllers\AgencyDashboardController::updateCriteriaWeights
* @see app/Http/Controllers/AgencyDashboardController.php:194
* @route '/admin/criteria/weights'
*/
export const updateCriteriaWeights = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateCriteriaWeights.url(options),
    method: 'post',
})

updateCriteriaWeights.definition = {
    methods: ["post"],
    url: '/admin/criteria/weights',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AgencyDashboardController::updateCriteriaWeights
* @see app/Http/Controllers/AgencyDashboardController.php:194
* @route '/admin/criteria/weights'
*/
updateCriteriaWeights.url = (options?: RouteQueryOptions) => {
    return updateCriteriaWeights.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AgencyDashboardController::updateCriteriaWeights
* @see app/Http/Controllers/AgencyDashboardController.php:194
* @route '/admin/criteria/weights'
*/
updateCriteriaWeights.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateCriteriaWeights.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AgencyDashboardController::updateCriteriaWeights
* @see app/Http/Controllers/AgencyDashboardController.php:194
* @route '/admin/criteria/weights'
*/
const updateCriteriaWeightsForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateCriteriaWeights.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AgencyDashboardController::updateCriteriaWeights
* @see app/Http/Controllers/AgencyDashboardController.php:194
* @route '/admin/criteria/weights'
*/
updateCriteriaWeightsForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateCriteriaWeights.url(options),
    method: 'post',
})

updateCriteriaWeights.form = updateCriteriaWeightsForm

/**
* @see \App\Http\Controllers\AgencyDashboardController::provisionStaff
* @see app/Http/Controllers/AgencyDashboardController.php:209
* @route '/admin/staff/provision'
*/
export const provisionStaff = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: provisionStaff.url(options),
    method: 'post',
})

provisionStaff.definition = {
    methods: ["post"],
    url: '/admin/staff/provision',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AgencyDashboardController::provisionStaff
* @see app/Http/Controllers/AgencyDashboardController.php:209
* @route '/admin/staff/provision'
*/
provisionStaff.url = (options?: RouteQueryOptions) => {
    return provisionStaff.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AgencyDashboardController::provisionStaff
* @see app/Http/Controllers/AgencyDashboardController.php:209
* @route '/admin/staff/provision'
*/
provisionStaff.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: provisionStaff.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AgencyDashboardController::provisionStaff
* @see app/Http/Controllers/AgencyDashboardController.php:209
* @route '/admin/staff/provision'
*/
const provisionStaffForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: provisionStaff.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AgencyDashboardController::provisionStaff
* @see app/Http/Controllers/AgencyDashboardController.php:209
* @route '/admin/staff/provision'
*/
provisionStaffForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: provisionStaff.url(options),
    method: 'post',
})

provisionStaff.form = provisionStaffForm

/**
* @see \App\Http\Controllers\AgencyDashboardController::exportAuditCsv
* @see app/Http/Controllers/AgencyDashboardController.php:234
* @route '/admin/audit/export'
*/
export const exportAuditCsv = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportAuditCsv.url(options),
    method: 'get',
})

exportAuditCsv.definition = {
    methods: ["get","head"],
    url: '/admin/audit/export',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AgencyDashboardController::exportAuditCsv
* @see app/Http/Controllers/AgencyDashboardController.php:234
* @route '/admin/audit/export'
*/
exportAuditCsv.url = (options?: RouteQueryOptions) => {
    return exportAuditCsv.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AgencyDashboardController::exportAuditCsv
* @see app/Http/Controllers/AgencyDashboardController.php:234
* @route '/admin/audit/export'
*/
exportAuditCsv.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportAuditCsv.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AgencyDashboardController::exportAuditCsv
* @see app/Http/Controllers/AgencyDashboardController.php:234
* @route '/admin/audit/export'
*/
exportAuditCsv.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: exportAuditCsv.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\AgencyDashboardController::exportAuditCsv
* @see app/Http/Controllers/AgencyDashboardController.php:234
* @route '/admin/audit/export'
*/
const exportAuditCsvForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportAuditCsv.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AgencyDashboardController::exportAuditCsv
* @see app/Http/Controllers/AgencyDashboardController.php:234
* @route '/admin/audit/export'
*/
exportAuditCsvForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportAuditCsv.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AgencyDashboardController::exportAuditCsv
* @see app/Http/Controllers/AgencyDashboardController.php:234
* @route '/admin/audit/export'
*/
exportAuditCsvForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportAuditCsv.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

exportAuditCsv.form = exportAuditCsvForm

/**
* @see \App\Http\Controllers\AgencyDashboardController::storeRule
* @see app/Http/Controllers/AgencyDashboardController.php:278
* @route '/admin/rules'
*/
export const storeRule = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeRule.url(options),
    method: 'post',
})

storeRule.definition = {
    methods: ["post"],
    url: '/admin/rules',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AgencyDashboardController::storeRule
* @see app/Http/Controllers/AgencyDashboardController.php:278
* @route '/admin/rules'
*/
storeRule.url = (options?: RouteQueryOptions) => {
    return storeRule.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AgencyDashboardController::storeRule
* @see app/Http/Controllers/AgencyDashboardController.php:278
* @route '/admin/rules'
*/
storeRule.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeRule.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AgencyDashboardController::storeRule
* @see app/Http/Controllers/AgencyDashboardController.php:278
* @route '/admin/rules'
*/
const storeRuleForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storeRule.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AgencyDashboardController::storeRule
* @see app/Http/Controllers/AgencyDashboardController.php:278
* @route '/admin/rules'
*/
storeRuleForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storeRule.url(options),
    method: 'post',
})

storeRule.form = storeRuleForm

/**
* @see \App\Http\Controllers\AgencyDashboardController::toggleRule
* @see app/Http/Controllers/AgencyDashboardController.php:307
* @route '/admin/rules/{id}/toggle'
*/
export const toggleRule = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: toggleRule.url(args, options),
    method: 'patch',
})

toggleRule.definition = {
    methods: ["patch"],
    url: '/admin/rules/{id}/toggle',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\AgencyDashboardController::toggleRule
* @see app/Http/Controllers/AgencyDashboardController.php:307
* @route '/admin/rules/{id}/toggle'
*/
toggleRule.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
    }

    return toggleRule.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AgencyDashboardController::toggleRule
* @see app/Http/Controllers/AgencyDashboardController.php:307
* @route '/admin/rules/{id}/toggle'
*/
toggleRule.patch = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: toggleRule.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\AgencyDashboardController::toggleRule
* @see app/Http/Controllers/AgencyDashboardController.php:307
* @route '/admin/rules/{id}/toggle'
*/
const toggleRuleForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: toggleRule.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AgencyDashboardController::toggleRule
* @see app/Http/Controllers/AgencyDashboardController.php:307
* @route '/admin/rules/{id}/toggle'
*/
toggleRuleForm.patch = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: toggleRule.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

toggleRule.form = toggleRuleForm

const AgencyDashboardController = { index, toggleVerifyOrganization, toggleVerifyCandidate, createProxyCandidate, updateCriteriaWeights, provisionStaff, exportAuditCsv, storeRule, toggleRule }

export default AgencyDashboardController