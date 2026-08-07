import OpportunityController from './OpportunityController'
import EmployerDashboardController from './EmployerDashboardController'
import DashboardController from './DashboardController'
import CandidateProfileController from './CandidateProfileController'
import NotificationController from './NotificationController'
import AgencyDashboardController from './AgencyDashboardController'
import Auth from './Auth'
import Settings from './Settings'

const Controllers = {
    OpportunityController: Object.assign(OpportunityController, OpportunityController),
    EmployerDashboardController: Object.assign(EmployerDashboardController, EmployerDashboardController),
    DashboardController: Object.assign(DashboardController, DashboardController),
    CandidateProfileController: Object.assign(CandidateProfileController, CandidateProfileController),
    NotificationController: Object.assign(NotificationController, NotificationController),
    AgencyDashboardController: Object.assign(AgencyDashboardController, AgencyDashboardController),
    Auth: Object.assign(Auth, Auth),
    Settings: Object.assign(Settings, Settings),
}

export default Controllers