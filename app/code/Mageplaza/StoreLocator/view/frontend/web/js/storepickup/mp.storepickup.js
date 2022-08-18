/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_StoreLocator
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */
var mpPickupData = {};
define([
    'jquery',
    'mage/calendar',
    'Mageplaza_Core/js/jquery.magnific-popup.min'
], function ($) {
    'use strict';

    $.widget('mageplaza.storepickup', {

        /**
         * @inheritDoc
         */
        _create: function () {
            var self = this;

            mpPickupData = {
                locationsData: self.options.locationsData,
                locationSessionUrl: self.options.location_session_url
            };

            self._observer();
        },

        _observer: function () {
            var self              = this,
                body              = $('body'),
                pickupAfterDays   = parseInt(self.options.pickupAfterDays, 10),
                date              = new Date(new Date().getTime() + (pickupAfterDays * 24 * 60 * 60 * 1000)),
                timeData          = {},
                locationId,
                currentYear       = date.getFullYear(),
                selectedLocId     = $('#mpstorepickup-loc-id-selected'),
                locationsData     = self.options.locationsData,
                inputStoreAddress = $('#address-selected'),
                listTimeEl        = $('.mpstorepickup-list-time-open'),
                listTimeSelect    = $('#mpstorepickup-list-time'),
                calendarEl        = $('#mpstorepickup-calendar');

            /** event click 'Select Store' button */
            body.on('click', '.btn-select-store', function (e) {
                e.preventDefault();
                var popupMap = $('#mpstorepickup-popup-map');

                if ($('.bh-sl-container').length === 0) {
                    $.ajax({
                        url: self.options.stores_map_url,
                        data: {isPickup: true},
                        type: 'post',
                        dataType: 'json',
                        showLoader: true,
                        success: function (res) {
                            popupMap.html(res.success);
                            popupMap.trigger('contentUpdated');
                        }
                    });
                }

                /** show popup */
                $.magnificPopup.open({
                    type: 'inline',
                    items: {
                        src: '#mpstorepickup-popup-content'
                    },
                    showCloseBtn: false,
                    callbacks: {
                        open: function () {
                            $('html, body').css({'overflow': 'hidden', 'position': 'relative', 'height': '100%'});
                        },
                        close: function () {
                            $('html, body').css({'overflow': 'unset', 'position': 'none'});
                        }
                    }
                });
            });

            /** event click Cancel button on popup store pickup */
            body.on('click', '.mpstorepickup-action-cancel', function (e) {
                e.preventDefault();
                selectedLocId.val('');
                inputStoreAddress.val('');
                $('#mpstorepickup-selected-info').hide();
                $('.mpstorepickup-date-selected').hide();
                $('.mpstorepickup-list-time-open').hide();
                $.magnificPopup.close();
            });


            /** event click Submit button on popup store pickup */
            body.on('click', '.mpstorepickup-action-submit', function (e) {
                e.preventDefault();
                var dataForm   = $('form#mpstorepickup-pickup-form'),
                    validate   = dataForm.validation('isValid'),
                    locationId = selectedLocId.val(),
                    timePickup = calendarEl.val() + ', ' + listTimeSelect.val();

                if (validate) {
                    /** set locationId selected to checkout session data **/
                    $.ajax({
                        url: self.options.location_session_url,
                        type: 'post',
                        data: {locationId: locationId, timePickup: timePickup, type: 'set'},
                        dataType: 'json',
                        showLoader: true,
                        success: function () {
                            $('.message.notice').hide();
                            $('.notice-select-store').hide();
                            $('#mpstorepickup-selected-info').show();
                            $('.mpstorepickup-selected-address span').html(inputStoreAddress.val());
                            $('.mpstorepickup-selected-time span').html(calendarEl.val() + ', ' + listTimeSelect.val());
                            $.magnificPopup.close();
                        }
                    });
                }
            });

            /** event click location on list locations */
            $.each(locationsData, function (id, locationData) {
                body.on('click', 'li.mpstorelocator-location-' + id, function () {
                    var storeAddress = locationData.street + ' ' +
                        locationData.region + ', ' +
                        locationData.city + ' ' +
                        locationData.countryId;

                    locationId = id;

                    /** update input store address when click location */
                    inputStoreAddress.val(storeAddress);

                    /** update date picker when click location */
                    calendarEl.val('');
                    timeData = locationData.timeData;


                    listTimeEl.hide();
                    selectedLocId.val(locationId);

                    /** datepicker **/
                    calendarEl.datepicker({
                        changeYear: true,
                        changeMonth: true,
                        yearRange: currentYear + ':2050',
                        minDate: date,
                        showsTime: true,
                        beforeShowDay: function (date) {
                            var currentDay     = date.getDay(),
                                currentDate    = date.getDate(),
                                currentMonth   = date.getMonth() + 1,
                                currentYear    = date.getFullYear(),
                                dateToCheck    = currentYear + '-' + currentMonth + '-' + currentDate,
                                isAvailableDay = timeData[currentDay].value === '1',
                                isHoliday      = self.checkHoliday(locationId, dateToCheck);

                            return [isAvailableDay && isHoliday];
                        },
                        beforeShow: function () {  //fix bug: not select date time picker on mobile
                            calendarEl.after(calendarEl.datepicker('widget'));
                        }
                    });

                    /** create Select Time To Pickup when selected day **/
                    $('.mpstorepickup-date-selected').show();
                    calendarEl.on('change', function () {
                        var time          = 0,
                            day           = new Date($(this).val()).getDay(),
                            timeDataOfDay = timeData[day],
                            hourStart     = parseInt(timeDataOfDay.from[0], 10),
                            hourEnd       = parseInt(timeDataOfDay.to[0], 10),
                            minStart      = timeDataOfDay.from[1],
                            minEnd        = timeDataOfDay.to[1];

                        listTimeEl.show();
                        listTimeSelect.html('');

                        for (var i = 0; i <= (hourEnd - hourStart); i++){
                            if (i === 0) {
                                time = hourStart + ':' + minStart + '-' + (hourStart + 1) + ':00';
                            } else if (i === (hourEnd - hourStart)) {
                                time = hourEnd + ':00' + '-' + hourEnd + ':' + minEnd;
                            } else {
                                time = (hourStart + i) + ':00-' + (hourStart + i + 1) + ':00';
                            }

                            listTimeSelect.append('<option value="' + time + '">' + time + '</option>');
                        }
                    })
                });
            });
        },

        /**
         * check show datepicker holiday
         *
         * @param locationId
         * @param dateToCheck
         * @returns {boolean}
         */
        checkHoliday: function (locationId, dateToCheck) {
            var check         = true,
                date          = new Date(dateToCheck.replace(/-/g, "/")).getTime(),
                locationsData = this.options.locationsData,
                holidaysData  = locationsData[locationId].holidayData;

            //toDate can\n't null
            $.each(holidaysData, function (holidayId, holidayData) {
                if (holidayData.to !== null){
                    var from = new Date(holidayData.from.replace(/-/g, "/")).getTime(),
                        to   = new Date(holidayData.to.replace(/-/g, "/")).getTime();

                    if (date >= from && date <= to) {
                        check = false;
                    }
                } else {
                    check = true;
                }
            });

            return check;
        }
    });

    return $.mageplaza.storepickup;
});
