import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
import portal from './portal'
import resumeC7deac from './resume'
import profile from './profile'
/**
* @see \App\Http\Controllers\CandidateProfileController::resume
* @see app/Http/Controllers/CandidateProfileController.php:18
* @route '/candidate/resume'
*/
export const resume = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: resume.url(options),
    method: 'post',
})

resume.definition = {
    methods: ["post"],
    url: '/candidate/resume',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CandidateProfileController::resume
* @see app/Http/Controllers/CandidateProfileController.php:18
* @route '/candidate/resume'
*/
resume.url = (options?: RouteQueryOptions) => {
    return resume.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CandidateProfileController::resume
* @see app/Http/Controllers/CandidateProfileController.php:18
* @route '/candidate/resume'
*/
resume.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: resume.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CandidateProfileController::resume
* @see app/Http/Controllers/CandidateProfileController.php:18
* @route '/candidate/resume'
*/
const resumeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: resume.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CandidateProfileController::resume
* @see app/Http/Controllers/CandidateProfileController.php:18
* @route '/candidate/resume'
*/
resumeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: resume.url(options),
    method: 'post',
})

resume.form = resumeForm

const candidate = {
    portal: Object.assign(portal, portal),
    resume: Object.assign(resume, resumeC7deac),
    profile: Object.assign(profile, profile),
}

export default candidate