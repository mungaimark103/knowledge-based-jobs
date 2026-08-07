import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\EmployerDashboardController::switchPortal
* @see app/Http/Controllers/EmployerDashboardController.php:21
* @route '/employer/portal-switch'
*/
export const switchPortal = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: switchPortal.url(options),
    method: 'get',
})

switchPortal.definition = {
    methods: ["get","head"],
    url: '/employer/portal-switch',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\EmployerDashboardController::switchPortal
* @see app/Http/Controllers/EmployerDashboardController.php:21
* @route '/employer/portal-switch'
*/
switchPortal.url = (options?: RouteQueryOptions) => {
    return switchPortal.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\EmployerDashboardController::switchPortal
* @see app/Http/Controllers/EmployerDashboardController.php:21
* @route '/employer/portal-switch'
*/
switchPortal.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: switchPortal.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\EmployerDashboardController::switchPortal
* @see app/Http/Controllers/EmployerDashboardController.php:21
* @route '/employer/portal-switch'
*/
switchPortal.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: switchPortal.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\EmployerDashboardController::switchPortal
* @see app/Http/Controllers/EmployerDashboardController.php:21
* @route '/employer/portal-switch'
*/
const switchPortalForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: switchPortal.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\EmployerDashboardController::switchPortal
* @see app/Http/Controllers/EmployerDashboardController.php:21
* @route '/employer/portal-switch'
*/
switchPortalForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: switchPortal.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\EmployerDashboardController::switchPortal
* @see app/Http/Controllers/EmployerDashboardController.php:21
* @route '/employer/portal-switch'
*/
switchPortalForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: switchPortal.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

switchPortal.form = switchPortalForm

/**
* @see \App\Http\Controllers\EmployerDashboardController::candidatePortalSwitch
* @see app/Http/Controllers/EmployerDashboardController.php:43
* @route '/candidate/portal-switch'
*/
export const candidatePortalSwitch = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: candidatePortalSwitch.url(options),
    method: 'get',
})

candidatePortalSwitch.definition = {
    methods: ["get","head"],
    url: '/candidate/portal-switch',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\EmployerDashboardController::candidatePortalSwitch
* @see app/Http/Controllers/EmployerDashboardController.php:43
* @route '/candidate/portal-switch'
*/
candidatePortalSwitch.url = (options?: RouteQueryOptions) => {
    return candidatePortalSwitch.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\EmployerDashboardController::candidatePortalSwitch
* @see app/Http/Controllers/EmployerDashboardController.php:43
* @route '/candidate/portal-switch'
*/
candidatePortalSwitch.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: candidatePortalSwitch.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\EmployerDashboardController::candidatePortalSwitch
* @see app/Http/Controllers/EmployerDashboardController.php:43
* @route '/candidate/portal-switch'
*/
candidatePortalSwitch.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: candidatePortalSwitch.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\EmployerDashboardController::candidatePortalSwitch
* @see app/Http/Controllers/EmployerDashboardController.php:43
* @route '/candidate/portal-switch'
*/
const candidatePortalSwitchForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: candidatePortalSwitch.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\EmployerDashboardController::candidatePortalSwitch
* @see app/Http/Controllers/EmployerDashboardController.php:43
* @route '/candidate/portal-switch'
*/
candidatePortalSwitchForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: candidatePortalSwitch.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\EmployerDashboardController::candidatePortalSwitch
* @see app/Http/Controllers/EmployerDashboardController.php:43
* @route '/candidate/portal-switch'
*/
candidatePortalSwitchForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: candidatePortalSwitch.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

candidatePortalSwitch.form = candidatePortalSwitchForm

/**
* @see \App\Http\Controllers\EmployerDashboardController::index
* @see app/Http/Controllers/EmployerDashboardController.php:56
* @route '/employer/dashboard'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/employer/dashboard',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\EmployerDashboardController::index
* @see app/Http/Controllers/EmployerDashboardController.php:56
* @route '/employer/dashboard'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\EmployerDashboardController::index
* @see app/Http/Controllers/EmployerDashboardController.php:56
* @route '/employer/dashboard'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\EmployerDashboardController::index
* @see app/Http/Controllers/EmployerDashboardController.php:56
* @route '/employer/dashboard'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\EmployerDashboardController::index
* @see app/Http/Controllers/EmployerDashboardController.php:56
* @route '/employer/dashboard'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\EmployerDashboardController::index
* @see app/Http/Controllers/EmployerDashboardController.php:56
* @route '/employer/dashboard'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\EmployerDashboardController::index
* @see app/Http/Controllers/EmployerDashboardController.php:56
* @route '/employer/dashboard'
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
* @see \App\Http\Controllers\EmployerDashboardController::updateOrganization
* @see app/Http/Controllers/EmployerDashboardController.php:138
* @route '/employer/organization'
*/
export const updateOrganization = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateOrganization.url(options),
    method: 'post',
})

updateOrganization.definition = {
    methods: ["post"],
    url: '/employer/organization',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\EmployerDashboardController::updateOrganization
* @see app/Http/Controllers/EmployerDashboardController.php:138
* @route '/employer/organization'
*/
updateOrganization.url = (options?: RouteQueryOptions) => {
    return updateOrganization.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\EmployerDashboardController::updateOrganization
* @see app/Http/Controllers/EmployerDashboardController.php:138
* @route '/employer/organization'
*/
updateOrganization.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateOrganization.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\EmployerDashboardController::updateOrganization
* @see app/Http/Controllers/EmployerDashboardController.php:138
* @route '/employer/organization'
*/
const updateOrganizationForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateOrganization.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\EmployerDashboardController::updateOrganization
* @see app/Http/Controllers/EmployerDashboardController.php:138
* @route '/employer/organization'
*/
updateOrganizationForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateOrganization.url(options),
    method: 'post',
})

updateOrganization.form = updateOrganizationForm

/**
* @see \App\Http\Controllers\EmployerDashboardController::storeJob
* @see app/Http/Controllers/EmployerDashboardController.php:187
* @route '/employer/jobs'
*/
export const storeJob = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeJob.url(options),
    method: 'post',
})

storeJob.definition = {
    methods: ["post"],
    url: '/employer/jobs',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\EmployerDashboardController::storeJob
* @see app/Http/Controllers/EmployerDashboardController.php:187
* @route '/employer/jobs'
*/
storeJob.url = (options?: RouteQueryOptions) => {
    return storeJob.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\EmployerDashboardController::storeJob
* @see app/Http/Controllers/EmployerDashboardController.php:187
* @route '/employer/jobs'
*/
storeJob.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeJob.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\EmployerDashboardController::storeJob
* @see app/Http/Controllers/EmployerDashboardController.php:187
* @route '/employer/jobs'
*/
const storeJobForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storeJob.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\EmployerDashboardController::storeJob
* @see app/Http/Controllers/EmployerDashboardController.php:187
* @route '/employer/jobs'
*/
storeJobForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storeJob.url(options),
    method: 'post',
})

storeJob.form = storeJobForm

const EmployerDashboardController = { switchPortal, candidatePortalSwitch, index, updateOrganization, storeJob }

export default EmployerDashboardController