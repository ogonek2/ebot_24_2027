import { useCallback, useEffect, useRef, useState } from "react";
import { fetchScheduledPopups } from "@/lib/api";
import {
  markPopupShown,
  type ScheduledPopupModal as ScheduledPopupData,
  unshownModals,
} from "@/lib/scheduledPopups";
import { useFeedback } from "@/context/FeedbackContext";
import ScheduledPopupModal from "./ScheduledPopupModal";

export default function ScheduledPopupManager() {
  const { isOpen: feedbackOpen } = useFeedback();
  const feedbackOpenRef = useRef(feedbackOpen);
  const [queue, setQueue] = useState<ScheduledPopupData[]>([]);
  const [current, setCurrent] = useState<ScheduledPopupData | null>(null);
  const pendingTimer = useRef<ReturnType<typeof setTimeout> | null>(null);
  const gapTimer = useRef<ReturnType<typeof setTimeout> | null>(null);
  const queueRef = useRef<ScheduledPopupData[]>([]);

  useEffect(() => {
    feedbackOpenRef.current = feedbackOpen;
  }, [feedbackOpen]);

  const clearTimers = useCallback(() => {
    if (pendingTimer.current) {
      clearTimeout(pendingTimer.current);
      pendingTimer.current = null;
    }
    if (gapTimer.current) {
      clearTimeout(gapTimer.current);
      gapTimer.current = null;
    }
  }, []);

  const tryShowFromQueue = useCallback(() => {
    clearTimers();
    if (feedbackOpenRef.current) return;

    const remaining = unshownModals(queueRef.current);
    if (!remaining.length) return;

    const first = remaining[0];
    const alreadyShownCount = queueRef.current.length - remaining.length;
    const initialDelaySec =
      alreadyShownCount === 0 ? (first.delay_before_show_seconds ?? 3) : 1;

    pendingTimer.current = setTimeout(() => {
      if (feedbackOpenRef.current) return;
      setCurrent(first);
    }, Math.max(0, initialDelaySec) * 1000);
  }, [clearTimers]);

  const scheduleNextAfterClose = useCallback(
    (closed: ScheduledPopupData) => {
      const remaining = unshownModals(queueRef.current);
      if (!remaining.length) return;

      const gapSec = closed.seconds_after_close_until_next ?? 300;
      gapTimer.current = setTimeout(() => {
        tryShowFromQueue();
      }, Math.max(0, gapSec) * 1000);
    },
    [tryShowFromQueue],
  );

  const handleClose = useCallback(() => {
    setCurrent((active) => {
      if (!active) return null;
      markPopupShown(active.id);
      scheduleNextAfterClose(active);
      return null;
    });
  }, [scheduleNextAfterClose]);

  const handleSubmitted = useCallback(() => {
    setCurrent((active) => {
      if (!active) return null;
      markPopupShown(active.id);
      scheduleNextAfterClose(active);
      return null;
    });
  }, [scheduleNextAfterClose]);

  useEffect(() => {
    let cancelled = false;

    fetchScheduledPopups()
      .then((data) => {
        if (cancelled || !data.modals?.length) return;
        queueRef.current = data.modals;
        setQueue(data.modals);
        tryShowFromQueue();
      })
      .catch(() => {});

    return () => {
      cancelled = true;
      clearTimers();
    };
  }, [clearTimers, tryShowFromQueue]);

  useEffect(() => {
    queueRef.current = queue;
  }, [queue]);

  useEffect(() => {
    if (feedbackOpen) {
      setCurrent(null);
      clearTimers();
      return;
    }
    if (queueRef.current.length) {
      tryShowFromQueue();
    }
  }, [feedbackOpen, clearTimers, tryShowFromQueue]);

  if (!current) return null;

  return (
    <ScheduledPopupModal modal={current} onClose={handleClose} onSubmitted={handleSubmitted} />
  );
}
